<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\PackGroupOption;
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
                    'text' => "{$product->name} ({$product->measurement})", // The text to display
                    'name' => $product->name,
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
                            'text' => "{$product->name} ({$variation->measurement})",
                            'name' => $product->name,
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

    public function searchPackOptions(Request $request)
    {
        $term = $request->input('q', '');

        if (empty($term)) {
            return response()->json(['results' => []]);
        }

        $options = PackGroupOption::with(['packGroup.pack'])
            ->whereHas('packGroup.pack', function ($query) use ($term) {
                // Search in the main Pack name
                $query->where('name', 'LIKE', '%' . $term . '%');
            })
            ->orWhereHas('packGroup', function ($query) use ($term) {
                // Search in the Group (Surface) name
                $query->where('surface', 'LIKE', '%' . $term . '%');
            })
            ->orWhere('option', 'LIKE', '%' . $term . '%') // Search in the Option number
            ->limit(20)
            ->get();

        // Format the results for Select2
        $results = $options->map(function ($option) {
            $packName = $option->packGroup->pack->name;
            $surfaceName = $option->packGroup->surface;
            $optionName = "Option " . $option->option;

            return [
                'id' => $option->id,
                'text' => "{$packName} | {$surfaceName} | {$optionName}",
                'price' => $option->price,
                'name_for_cart' => "{$packName} | {$optionName} | {$surfaceName}",
            ];
        });

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
        // Eager load all relationships needed to populate the form
        $sale->load('customer.user', 'categoryItems.product', 'categoryItems.variation', 'packItems', 'orderTax');

        $customers = Customer::with('user')->get();
        $taxes = Tax::where('status', true)->get();
        // echo '<pre>';
        // print_r($sale->toArray());
        // echo '</pre>';
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
            'items' => 'sometimes|array',
        ]);

        try {
            DB::transaction(function () use ($request, $sale) {
                // 1. Update the main Sale record with the summary data
                $sale->update([
                    'invoice_number' => $request->invoice_number,
                    'customer_id' => $request->customer_id,
                    'sales_date' => $request->sales_date,
                    'order_status' => $request->order_status,
                    'sub_total' => $request->input('sub_total', 0),
                    'order_tax_id' => $request->order_tax_id,
                    'order_tax_amount' => $request->input('order_tax_amount', 0),
                    'discount' => $request->input('discount_amount', 0),
                    'discount_type' => $request->input('discount_type'),
                    'discount_rate' => $request->input('discount_value'),
                    'shipping' => $request->input('shipping', 0),
                    'grand_total' => $request->grand_total,
                ]);

                // --- THIS IS THE NEW DIFF AND SYNC LOGIC ---

                $submittedItems = collect($request->input('items', []));

                // Separate submitted items by type
                $submittedCategoryItems = $submittedItems->where('type', 'category');
                $submittedPackItems = $submittedItems->where('type', 'pack');

                // Get IDs of submitted items that already have a database ID
                $submittedCategoryItemIds = $submittedCategoryItems->pluck('sale_item_id')->filter();
                $submittedPackItemIds = $submittedPackItems->pluck('sale_item_id')->filter();

                // 1. DELETE items that are in the DB but not in the submission
                $sale->categoryItems()->whereNotIn('id', $submittedCategoryItemIds)->delete();
                $sale->packItems()->whereNotIn('id', $submittedPackItemIds)->delete();

                // 2. UPDATE existing items and CREATE new ones
                foreach ($submittedCategoryItems as $itemData) {
                    $sale->categoryItems()->updateOrCreate(
                        ['id' => $itemData['sale_item_id'] ?? null], // Condition to find the item
                        [ // Data to update or create with
                            'product_id' => $itemData['id'],
                            'product_variation_id' => $itemData['variation_id'] ?? null,
                            'product_name' => $itemData['name'],
                            'quantity' => $itemData['quantity'],
                            'unit_price' => $itemData['price'],
                            'total_price' => ($itemData['price'] * $itemData['quantity']),
                        ]
                    );
                }

                foreach ($submittedPackItems as $itemData) {
                    $packItem = $sale->packItems()->updateOrCreate(
                        ['id' => $itemData['sale_item_id'] ?? null],
                        [
                            'pack_group_option_id' => $itemData['id'],
                            'pack_display_name' => $itemData['name'],
                            'quantity' => $itemData['quantity'],
                            'unit_price' => $itemData['price'],
                            'total_price' => ($itemData['price'] * $itemData['quantity']),
                        ]
                    );

                    // After updating/creating the pack item, we must sync its constituent parts
                    // The simplest reliable way is still to delete and re-create for the sub-items
                    $packItem->constituentItems()->delete();
                    $option = PackGroupOption::find($itemData['id']);
                    if ($option) {
                        // ... (The logic from your createSaleItems to loop and create constituent items goes here)
                    }
                }
            });
        } catch (\Exception $e) {
            \Log::error('Sale Update Failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while updating the sale.')->withInput();
        }

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    /**
     * Helper method to create all sale items (category and pack).
     */
    private function createSaleItems(Sale $sale, array $items): void
    {
        foreach ($items as $cartItem) {
            $itemType = $cartItem['type'] ?? null;

            if ($itemType === 'category') {
                $sale->categoryItems()->create([
                    'product_id' => $cartItem['id'],
                    'product_variation_id' => $cartItem['variation_id'] ?? null,
                    'product_name' => $cartItem['name'],
                    'quantity' => $cartItem['quantity'],
                    'unit_price' => $cartItem['price'],
                    'total_price' => ($cartItem['price'] * $cartItem['quantity']),
                ]);
            } elseif ($itemType === 'pack') {
                $packSaleItem = $sale->packItems()->create([
                    'pack_group_option_id' => $cartItem['id'],
                    'pack_display_name' => $cartItem['name'],
                    'quantity' => $cartItem['quantity'],
                    'unit_price' => $cartItem['price'],
                    'total_price' => ($cartItem['price'] * $cartItem['quantity']),
                ]);

                $option = PackGroupOption::find($cartItem['id']);
                if ($option) {
                    $attachedProducts = $option->products;
                    foreach ($attachedProducts as $product) {
                        $packProduct = $product->pivot;
                        $selectedItems = $packProduct->selectedItems;
                        if ($selectedItems->isNotEmpty()) {
                            foreach ($selectedItems as $selectedItem) {
                                $packSaleItem->constituentItems()->create([
                                    'pack_product_id' => $packProduct->id,
                                    'pack_product_selected_variation_id' => $selectedItem->id,
                                    'product_name' => $selectedItem->variation->product->name ?? $selectedItem->product->name,
                                ]);
                            }
                        }
                    }
                }
            }
        }
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
