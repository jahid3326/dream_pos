<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Tax;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load customer and user to avoid N+1 query issues
        $sales = Sale::with('customer.user')->latest()->paginate(15);
        return view('sales.index', compact('sales'));
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
        $sale->load('customer.user', 'items.product', 'items.variation', 'orderTax');
        return view('sales.show', compact('sale'));
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
