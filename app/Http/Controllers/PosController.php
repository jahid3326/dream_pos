<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ProductVariation;
use App\Models\Tax;
use App\Models\Pack;
use App\Models\PackGroupOption;
use App\Models\PackProduct;
use App\Models\Sale;
use App\Models\SalePackProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Data for Pack Building (deeply nested)
        $packs = Pack::with('groups.options')->orderBy('name')->get();

        // 2. Data for Standard Product Browsing (hierarchical)
        $parentCategories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        // 3. Flattened list of ALL products and variations for the grid
        /*
        $products = Product::with(['variations', 'category', 'tax'])->get()->flatMap(function ($product) {
            if ($product->type === 'single') {
                return collect([$product]);
            }
            return $product->variations->map(function ($variation) use ($product) {
                $variation->parent_name = $product->name;
                $variation->category_id = $product->category_id; // Ensure category is accessible
                return $variation;
            });
        });
        */

        // 4. Common Data
        $customers = Customer::with('user')->get();
        $taxes = Tax::where('status', true)->get();

        return view('pos.index', compact('packs', 'parentCategories', 'customers', 'taxes'));
    }

    // in PosController.php

    // in PosController.php

    // in PosController.php

    public function getPackOptionProducts(PackGroupOption $option)
    {
        try {

            // 1. Load the primary relationship: the parent products attached to this option.
            // This is a simple, reliable call.
            $option->load('products');

            // 2. Loop through each of these parent products to load their nested data.
            // This is more explicit and avoids the collection->load() error.
            foreach ($option->products as $product) {

                // 3. For each product, we are guaranteed to have a 'pivot' attribute,
                //    which is an instance of our PackProduct model.
                //    We then load the relationship ON THAT SPECIFIC PIVOT INSTANCE.
                $product->pivot->load(['selectedVariations' => function ($query) {
                    $query->with(['product.tax', 'variation.tax', 'variation.product']);
                }]);
            }

            // 4. After the loop, the $option->products collection is fully populated with all
            //    the nested data. We can now return it.
            return response()->json($option->products);
        } catch (\Exception $e) {
            \Log::error("Error fetching pack option products: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['error' => 'Could not load products for this option.'], 500);
        }
    }

    public function getCategoryProducts(Category $category)
    {
        try {
            // Find all products directly in this category or variations whose parent is in this category
            $products = Product::with(['variations', 'tax'])
                ->where('category_id', $category->id)
                ->get();

            // Transform the data into the same flat list format our JS expects
            $displayProducts = collect();
            foreach ($products as $product) {
                if ($product->type === 'single') {
                    $displayProducts->push([
                        'id' => $product->id,
                        'variation_id' => null,
                        'name' => $product->name,
                        'measurement' => $product->measurement,
                        'price' => $product->sale_price,
                        'image' => $product->product_image ? asset('public/storage/' . $product->product_image) : asset('public/storage/images/default_image.png'),
                        'measurement' => $product->measurement,
                        'category_id' => $product->category_id,
                    ]);
                } else {
                    foreach ($product->variations as $variation) {
                        $displayProducts->push([
                            'id' => $product->id,
                            'variation_id' => $variation->id,
                            'name' => $product->name,
                            'measurement' => $variation->measurement,
                            'price' => $variation->sale_price,
                            'image' => $variation->image ? asset('public/storage/' . $variation->image) : asset('public/storage/images/default_image.png'),
                            'measurement' => $variation->measurement,
                            'category_id' => $product->category_id,
                        ]);
                    }
                }
            }

            return response()->json($displayProducts);
        } catch (\Exception $e) {
            \Log::error("Error fetching pack option products: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['error' => 'Could not load products for this option.'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Store a new sale WITHOUT a payment record (Invoice Only).
     */
    public function storeInvoice(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'grand_total' => 'required|numeric',
            'items' => 'required|array|min:1',
        ]);

        $sale = null;
        try {
            DB::transaction(function () use ($request, &$sale) {
                $sale = $this->createSaleRecord($request, 'on process');
                $this->createSaleItems($sale, $request->items);
            });
        } catch (\Exception $e) {
            \Log::error('Invoice Creation Failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error creating invoice.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice generated successfully!',
            'redirect_url' => route('sales.show', $sale->id)
        ]);
    }

    /**
     * Store a new sale WITH a payment record.
     */
    public function storeWithPayment(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'grand_total' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'payment_mode' => 'required|string',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|gte:0|max:' . $request->grand_total,
            'payment_note' => 'nullable|string',
        ]);

        $sale = null;
        try {
            DB::transaction(function () use ($request, &$sale) {
                $sale = $this->createSaleRecord($request, 'delivered'); // Paid sales are 'delivered' or 'completed'
                $this->createSaleItems($sale, $request->items);

                // Create the Payment Record
                $sale->payments()->create([
                    'payment_mode' => $request->payment_mode,
                    'amount' => $request->amount,
                    'payment_date' => $request->payment_date,
                    'note' => $request->payment_note,
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Sale with Payment Failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error creating sale with payment.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment successful and invoice generated!',
            'redirect_url' => route('sales.show', $sale->id)
        ]);
    }

    /**
     * Helper method to create the main Sale record.
     */
    private function createSaleRecord(Request $request, string $status)
    {
        return Sale::create([
            'invoice_number' => 'SALE-' . str_pad((Sale::latest()->first()?->id + 1), 2, '0', STR_PAD_LEFT),
            'customer_id' => $request->customer_id,
            'sales_date' => now(),
            'order_status' => $status,
            'sub_total' => $request->input('sub_total', 0),
            'order_tax_id' => $request->order_tax_id,
            'order_tax_amount' => $request->input('order_tax_amount', 0),
            'discount' => $request->input('discount_amount', 0),
            'discount_type' => $request->input('discount_type'),
            'discount_rate' => ($request->input('discount_type') === 'percentage') ? $request->input('discount_value') : null,
            'shipping' => $request->input('shipping', 0),
            'grand_total' => $request->grand_total,
            'order_taken_by' => Auth::id(),
        ]);
    }

    /**
     * Helper method to create all sale items (category and pack).
     */
    private function createSaleItems(Sale $sale, array $items)
    {
        foreach ($items as $cartItem) {
            if ($cartItem['type'] === 'category') {
                $sale->categoryItems()->create([
                    'product_id' => $cartItem['id'],
                    'product_variation_id' => $cartItem['variation_id'],
                    'product_name' => $cartItem['name'],
                    'quantity' => $cartItem['quantity'],
                    'unit_price' => $cartItem['price'],
                    'total_price' => $cartItem['price'] * $cartItem['quantity'],
                ]);
            } elseif ($cartItem['type'] === 'pack') {
                // It's a pack, save the main line item to 'sale_pack_products'
                $packSaleItem = $sale->packItems()->create([
                    'pack_group_option_id' => $cartItem['id'], // 'id' from the cart is the option_id
                    'pack_display_name' => $cartItem['name'],
                    'quantity' => $cartItem['quantity'],
                    'unit_price' => $cartItem['price'],
                    'total_price' => $cartItem['price'] * $cartItem['quantity'],
                ]);

                // --- THIS IS THE CORRECTED LOGIC FOR CONSTITUENT ITEMS ---

                // 1. Find the PackGroupOption that was sold.
                $option = PackGroupOption::find($cartItem['id']);

                if ($option) {
                    // 2. Query the 'pack_product' pivot table directly to get the link records.
                    //    This ensures we get the pivot model instances correctly.
                    $packProducts = PackProduct::where('pack_group_option_id', $option->id)
                        ->with('selectedVariations.product', 'selectedVariations.variation.product')
                        ->get();

                    // 3. Loop through the pivot records.
                    foreach ($packProducts as $packProduct) {
                        $selectedItems = $packProduct->selectedVariations;

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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
