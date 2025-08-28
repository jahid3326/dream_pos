<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pack;
use App\Models\Product;
use App\Models\PackGroupOption;
use App\Models\PackProduct;
use App\Models\PackProductSelectedVariation;

class ManagePackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load the entire nested relationship tree for the view
        $packs = Pack::with([
            'groups' => function ($query) {
                $query->orderBy('surface');
            },
            'groups.options' => function ($query) {
                $query->orderBy('option');
            },
            'groups.options.products' => function ($query) {
                // Further eager load details for the products
                $query->with('supplier.user');
            }
        ])->latest()->get();

        // Fetch the child categories here
        $childCategories = \App\Models\Category::whereNotNull('parent_id')->orderBy('name')->get();

        // Prepare a list of all PARENT products for the "Add Product" modal
        $allProducts = Product::with('supplier.user')->get()->map(function ($product) {
            // We are no longer dealing with variations here, just the parent product.
            // The value we will use in the checkbox will be the product ID itself.
            // We add a display_sku for the user to see.
            $product->display_sku = ($product->type === 'single') ? $product->sku : '(Variation)';
            return $product;
        });

        // echo "<pre>";
        // print_r($packs->toArray());
        // echo "</pre>";

        return view('manage-packs.index', compact('packs', 'allProducts', 'childCategories'));
    }

    public function attachProducts(Request $request, PackGroupOption $option)
    {
        $request->validate(['product_ids' => 'required|array']);

        // Determine the starting position for the new items
        $lastPosition = $option->products()->max('position') ?? 0;

        // Prepare the data with position values
        $productsToAttach = [];
        foreach ($request->product_ids as $index => $productId) {
            $productsToAttach[$productId] = ['position' => $lastPosition + $index + 1];
        }

        // syncWithoutDetaching with pivot data
        $option->products()->syncWithoutDetaching($productsToAttach);

        // Return the newly added products for the dynamic view update
        $newlyAddedProducts = Product::with('supplier.user', 'variations')
            ->whereIn('id', $request->product_ids)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Products appended successfully.',
            'products' => $newlyAddedProducts
        ]);
    }

    public function detachAllProducts(PackGroupOption $option)
    {
        // detach() with no arguments removes all associations for this option.
        $option->products()->detach();

        return response()->json(['success' => true, 'message' => 'All products removed successfully.']);
    }

    public function detachProduct(PackGroupOption $option, Product $product)
    {
        $option->products()->detach($product->id);
        return response()->json(['success' => true, 'message' => 'Product removed successfully.']);
    }

    public function reorderProducts(Request $request, PackGroupOption $option)
    {
        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $item) {
            $option->products()->updateExistingPivot($item['id'], ['position' => $item['position']]);
        }

        return response()->json(['success' => true, 'message' => 'Order updated successfully.']);
    }

    /**
     * Get data for the "Manage Items/Variations" modal.
     */
    public function getPackProductData(PackProduct $packProduct)
    {
        // Load the parent product (with all its available variations) and the currently selected variations
        $packProduct->load('product.variations', 'selectedVariations');

        // Get an array of IDs of the variations that are already selected
        $selectedVariationIds = $packProduct->selectedVariations->pluck('id');

        return response()->json([
            'product_name' => $packProduct->product->name,
            'available_variations' => $packProduct->product->variations,
            'selected_variation_ids' => $selectedVariationIds,
        ]);
    }

    /**
     * Save the selected items/variations for a pack product.
     */
    public function saveSelectedVariations(Request $request, PackProduct $packProduct)
    {
        $request->validate(['variation_ids' => 'nullable|array']);
        $variationIds = $request->input('variation_ids', []);

        // sync() is perfect here. It will attach the checked variations and detach any that were unchecked.
        $packProduct->selectedVariations()->sync($variationIds);

        return response()->json(['success' => true, 'count' => count($variationIds)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

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
