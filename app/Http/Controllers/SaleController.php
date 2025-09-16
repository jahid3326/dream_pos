<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Tax;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDF;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager-load all necessary nested data for the complex view
        $sales = Sale::with([
            'customer.user',
            'payments',
            'categoryItems.product.category', // For standard items
            'categoryItems.variation', // For standard items
            // --- THIS IS THE CORRECTED EAGER LOADING FOR PACKS ---
            'packItems.constituentItems' => function ($query) {
                // For each constituent item, load its definition...
                $query->with([
                    'packProduct.product.category',
                    'packProductSelectedVariation' => function ($q) {
                        // ...and on that definition, load the final product and variation details.
                        $q->with(['product.category', 'variation']);
                    }
                ]);
            }
        ])->latest()->paginate(10);

        $salesArray = $sales->toArray();

        // Now $salesArray is a pure PHP array
        /*
        echo '<pre>';
        print_r($salesArray);
        echo '</pre>';
        */

        // Add calculated properties to each sale model for easier use in the view
        $sales->each(function ($sale) {
            $sale->paid_amount = $sale->payments->sum('amount');
            $sale->due_amount = $sale->grand_total - $sale->paid_amount;
            $sale->payment_status = $sale->due_amount <= 0 ? 'Paid' : ($sale->paid_amount > 0 ? 'Deposit' : 'Unpaid');
        });
        return view('sales.index', compact('sales'));
    }

    /**
     * Store a new payment for an existing sale via AJAX.
     */
    public function addPayment(Request $request, Sale $sale)
    {
        // Calculate the current due amount for validation
        $paidAmount = $sale->payments()->sum('amount');
        $dueAmount = $sale->grand_total - $paidAmount;

        $validator = Validator::make($request->all(), [
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string',
            // The amount paid cannot be more than the amount due
            'amount' => 'required|numeric|gte:0|max:' . $dueAmount,
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()], 422);
        }

        // Create the new payment record
        $sale->payments()->create($request->all());

        // After adding the new payment, recalculate and update the sale's status if needed
        $newPaidAmount = $sale->payments()->sum('amount');
        if ($newPaidAmount >= $sale->grand_total) {
            $sale->update(['order_status' => 'delivered']); // Or 'paid'
        } else {
            $sale->update(['order_status' => 'in process']); // Or keep as 'pending'
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment added successfully!',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::with('user')->get();
        $taxes = Tax::where('status', true)->get();

        // Generate a unique invoice number
        $latestSale = Sale::latest()->first();
        $invoiceNumber = 'SALE-' . str_pad(($latestSale ? $latestSale->id + 1 : 1), 4, '0', STR_PAD_LEFT);

        return view('sales.create', compact('customers', 'taxes', 'invoiceNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string|unique:sales,invoice_number',
            'customer_id' => 'required|exists:customers,id',
            'sales_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $sale = Sale::create([
                'invoice_number' => $request->invoice_number,
                'customer_id' => $request->customer_id,
                'sales_date' => $request->sales_date,
                'order_status' => $request->order_status,
                'sub_total' => $request->sub_total,
                'order_tax_id' => $request->order_tax_id,
                'order_tax_amount' => $request->order_tax_amount,
                'discount' => $request->discount,
                'shipping' => $request->shipping,
                'grand_total' => $request->grand_total,
                'terms_and_conditions' => $request->terms_and_conditions,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $itemData) {
                $sale->items()->create($itemData);
            }
        });

        return redirect()->route('sales.index')->with('success', 'Sale created successfully.');
    }

    public function searchProducts(Request $request)
    {
        // Select2 sends the search term as 'q' by default in its AJAX request.
        // We provide an empty string as a fallback.
        $term = $request->input('q', '');

        // If the search term is empty, return an empty result set immediately.
        if (empty($term)) {
            return response()->json(['results' => []]);
        }

        // Start the query
        $products = Product::with('variations')
            ->where(function ($query) use ($term) {
                // Search in the main product name or SKU
                $query->where('name', 'LIKE', '%' . $term . '%')
                    ->orWhere('sku', 'LIKE', '%' . $term . '%');
            })
            ->orWhereHas('variations', function ($query) use ($term) {
                // Search in the variation's SKU or measurement
                $query->where('sku', 'LIKE', '%' . $term . '%')
                    ->orWhere('measurement', 'LIKE', '%' . $term . '%');
            })
            ->limit(20) // Limit the number of results for performance
            ->get();

        $results = [];
        foreach ($products as $product) {
            if ($product->type === 'single') {
                $results[] = [
                    'id' => $product->id, // A unique ID for the option
                    'text' => "{$product->name} (SKU: {$product->sku})", // The text to display
                    'price' => $product->sale_price,
                    'tax_id' => $product->tax_id,
                    'variation_id' => null,
                ];
            } else {
                foreach ($product->variations as $variation) {
                    // Important: Check if the variation itself matches the search term
                    if (
                        stripos($product->name, $term) !== false ||
                        stripos($variation->sku, $term) !== false ||
                        stripos($variation->measurement, $term) !== false
                    ) {
                        $results[] = [
                            'id' => $product->id,
                            'variation_id' => $variation->id,
                            'text' => "{$product->name} - {$variation->measurement} (SKU: {$variation->sku})",
                            'price' => $variation->sale_price,
                            'tax_id' => $variation->tax_id
                        ];
                    }
                }
            }
        }

        // Select2 expects the data to be in a JSON object with a 'results' key.
        return response()->json(['results' => $results]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        // Eager load all necessary data for the invoice
        $sale->load([
            'customer.user',
            'user', // The user who made the sale
            'orderTax',
            'categoryItems.product.category',
            'categoryItems.variation.product',
            'packItems.constituentItems.packProduct.product',
            'packItems.constituentItems.packProductSelectedVariation.variation.product',
            'payments'
        ]);

        // --- THIS IS THE KEY ADDITION ---
        // Calculate and add payment details directly to the sale object
        $sale->paid_amount = $sale->payments->sum('amount');
        $sale->due_amount = $sale->grand_total - $sale->paid_amount;
        $sale->payment_status = $sale->due_amount <= 0 ? 'Paid' : ($sale->paid_amount > 0 ? 'Deposit' : 'Unpaid');
        return view('sales.show', compact('sale'));
    }

    public function viewInvoicePdf(Sale $sale)
    {
        // Eager load all necessary data for the invoice
        $sale->load([
            'customer.user',
            'user', // The user who made the sale
            'orderTax',
            'categoryItems.product.category',
            'categoryItems.variation.product',
            'packItems.constituentItems.packProduct.product',
            'packItems.constituentItems.packProductSelectedVariation.variation.product',
            'payments'
        ]);

        // Calculate payment details
        $sale->paid_amount = $sale->payments->sum('amount');
        $sale->due_amount = $sale->grand_total - $sale->paid_amount;
        $sale->payment_status = $sale->due_amount <= 0 ? 'Paid' : ($sale->paid_amount > 0 ? 'Diposit' : 'Unpaid');

        // Pass the sale data to our dedicated PDF view
        $pdf = PDF::loadView('sales.invoice-pdf', compact('sale'));

        // --- THIS IS THE KEY CHANGE ---
        // Instead of download(), use stream().
        // This sets the Content-Disposition header to 'inline' instead of 'attachment'.
        $filename = 'Invoice-' . $sale->invoice_number . '.pdf';
        return $pdf->stream($filename);
    }

    public function printInvoice(Sale $sale)
    {
        // Eager load all necessary data for the invoice
        $sale->load([
            'customer.user',
            'user', // The user who made the sale
            'orderTax',
            'categoryItems.product.category',
            'categoryItems.variation.product',
            'packItems.constituentItems.packProduct.product',
            'packItems.constituentItems.packProductSelectedVariation.variation.product',
            'payments'
        ]);

        // Calculate payment details
        $sale->paid_amount = $sale->payments->sum('amount');
        $sale->due_amount = $sale->grand_total - $sale->paid_amount;
        $sale->payment_status = $sale->due_amount <= 0 ? 'Paid' : ($sale->paid_amount > 0 ? 'Diposit' : 'Unpaid');

        return view('sales.invoice-print', compact('sale'));
    }

    public function downloadInvoicePdf(Sale $sale)
    {
        // Eager load all necessary data for the invoice
        $sale->load([
            'customer.user',
            'user', // The user who made the sale
            'orderTax',
            'categoryItems.product.category',
            'categoryItems.variation.product',
            'packItems.constituentItems.packProduct.product',
            'packItems.constituentItems.packProductSelectedVariation.variation.product',
            'payments'
        ]);

        // Calculate payment details
        $sale->paid_amount = $sale->payments->sum('amount');
        $sale->due_amount = $sale->grand_total - $sale->paid_amount;
        $sale->payment_status = $sale->due_amount <= 0 ? 'Paid' : ($sale->paid_amount > 0 ? 'Diposit' : 'Unpaid');

        // Pass the sale data to our dedicated PDF view
        $pdf = PDF::loadView('sales.invoice-pdf', compact('sale'));

        // --- THIS IS THE KEY CHANGE ---
        // Instead of download(), use stream().
        // This sets the Content-Disposition header to 'inline' instead of 'attachment'.
        $filename = 'Invoice-' . $sale->invoice_number . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Fetch all payments for a specific sale via AJAX.
     */
    public function getPayments(Sale $sale)
    {
        // Eager load the payments relationship
        $sale->load('payments');

        // You can format the data here if you want, or just send the collection
        $payments = $sale->payments->map(function ($payment) {
            return [
                'date' => $payment->payment_date->format('d M, Y'),
                'mode' => $payment->payment_mode,
                'note' => $payment->note,
                'amount' => number_format($payment->amount, 2),
            ];
        });

        return response()->json([
            'success' => true,
            'invoice_number' => $sale->invoice_number,
            'payments' => $payments
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        // Eager load all relationships
        $sale->load('items.product', 'items.variation');

        $customers = Customer::with('user')->get();
        $taxes = Tax::where('status', true)->get();

        return view('sales.edit', compact('sale', 'customers', 'taxes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'invoice_number' => 'required|string|unique:sales,invoice_number,' . $sale->id,
            'customer_id' => 'required|exists:customers,id',
            'sales_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request, $sale) {
            $sale->update([
                'invoice_number' => $request->invoice_number,
                'customer_id' => $request->customer_id,
                'sales_date' => $request->sales_date,
                'order_status' => $request->order_status,
                'sub_total' => $request->sub_total,
                'order_tax_id' => $request->order_tax_id,
                'order_tax_amount' => $request->order_tax_amount,
                'discount' => $request->discount,
                'shipping' => $request->shipping,
                'grand_total' => $request->grand_total,
                'terms_and_conditions' => $request->terms_and_conditions,
                'notes' => $request->notes,
            ]);

            $submittedItemIds = [];
            foreach ($request->items as $itemData) {
                $item = $sale->items()->updateOrCreate(
                    ['id' => $itemData['id'] ?? null], // Find by ID or create new
                    $itemData
                );
                $submittedItemIds[] = $item->id;
            }

            // Delete items that were removed from the form
            $sale->items()->whereNotIn('id', $submittedItemIds)->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        // The onDelete('cascade') in the migration will delete all related sale_items
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }
}
