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
    public function store(Request $request)
    {
        //
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
