<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pack;
use App\Models\Product;
use App\Models\PackGroupOption;
use App\Models\PackProduct;
use App\Models\PackProductSelectedVariation;
use Illuminate\Support\Facades\DB;

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
        /*
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

        $newlyAddedProducts->each(function ($product) {
            $product->pivot->load('selectedVariations');
        });
        */
        DB::transaction(function () use ($request, $option) {
            $lastPosition = $option->products()->max('position') ?? 0;
            $productsToAttach = [];
            foreach ($request->product_ids as $index => $productId) {
                $productsToAttach[$productId] = ['position' => $lastPosition + $index + 1];
            }
            $option->products()->syncWithoutDetaching($productsToAttach);
        });

        // This query will now work because the relationship provides the correct pivot model
        $newlyAddedProducts = $option->products()
            ->whereIn('product_id', $request->product_ids)
            ->with(['supplier.user', 'variations'])
            ->get();

        // This loop will now work because $product->pivot is a valid PackProduct object
        $newlyAddedProducts->each(function ($product) {
            $product->pivot->load('selectedVariations');
        });

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
        // Eager-load the parent product and the currently selected items
        // We are now using the correct 'selectedVariations' relationship
        $packProduct->load('product', 'selectedVariations');

        // --- THIS IS THE CORRECTED LOGIC ---
        $selectedIds = collect();
        // Loop through the PackProductSelectedVariation records
        foreach ($packProduct->selectedVariations as $item) {
            // Check if the product_variation_id is set on this record
            if ($item->product_variation_id) {
                // If yes, it's a variation
                $selectedIds->push('v-' . $item->product_variation_id);
            } else {
                // If no, it's a single product
                $selectedIds->push('p-' . $item->product_id);
            }
        }

        // This logic for getting all possible items remains correct
        $allItems = Product::with(['variations', 'category'])->get()->flatMap(function ($product) {
            if ($product->type === 'single') {
                $product->image = $product->product_image ? asset('storage/' . $product->product_image) : asset('storage/images/default_image.png');
                $product->display_name = $product->name;
                $product->measurement = $product->measurement;
                $product->unique_id = 'p-' . $product->id;
                $product->category_id_for_filter = $product->category_id;
                return collect([$product]);
            }
            return $product->variations->map(function ($variation) use ($product) {
                $variation->image = $variation->image ? asset('storage/' . $variation->image) : asset('storage/images/default_image.png');
                $variation->display_name = $product->name;
                $variation->measurement = $variation->measurement;
                $variation->unique_id = 'v-' . $variation->id;
                $variation->category_id_for_filter = $product->category_id;
                return $variation;
            });
        });

        return response()->json([
            'product_name' => $packProduct->product->name,
            'all_selectable_items' => $allItems,
            'selected_ids' => $selectedIds->unique()->values(), // Use unique() as an extra safeguard
        ]);
    }

    /**
     * Save the selected items/variations for a pack product.
     */
    public function saveSelectedItems(Request $request, PackProduct $packProduct)
    {
        $request->validate(['item_ids' => 'nullable|array']);

        try {
            DB::transaction(function () use ($request, $packProduct) {

                // 1. Get a simple PHP array of item IDs submitted from the form.
                $submittedUniqueIds = $request->input('item_ids', []);

                // 2. Get the currently existing items from the database.
                $existingItems = $packProduct->fresh()->selectedVariations;

                // 3. Create a simple PHP array of existing unique IDs ('p-1', 'v-5', etc.).
                $existingUniqueIdsArray = $existingItems->map(function ($item) {
                    if ($item->product_variation_id) {
                        return 'v-' . $item->product_variation_id;
                    }
                    if ($item->product_id) {
                        return 'p-' . $item->product_id;
                    }
                    return null;
                })->filter()->toArray(); // Convert to a plain array.

                // 4. Determine which items to DELETE using plain array functions.
                // These are items that exist in the database but were NOT in the form submission.
                $uniqueIdsToDelete = array_diff($existingUniqueIdsArray, $submittedUniqueIds);

                if (!empty($uniqueIdsToDelete)) {
                    $itemIdsToActuallyDelete = [];
                    foreach ($existingItems as $item) {
                        $uniqueId = $item->product_variation_id ? 'v-' . $item->product_variation_id : 'p-' . $item->product_id;
                        if (in_array($uniqueId, $uniqueIdsToDelete)) {
                            $itemIdsToActuallyDelete[] = $item->id;
                        }
                    }
                    if (!empty($itemIdsToActuallyDelete)) {
                        PackProductSelectedVariation::whereIn('id', $itemIdsToActuallyDelete)->delete();
                    }
                }

                // 5. Determine which items to CREATE using plain array functions.
                // These are items that were in the form submission but are NOT in the database yet.
                $uniqueIdsToCreate = array_diff($submittedUniqueIds, $existingUniqueIdsArray);

                if (!empty($uniqueIdsToCreate)) {
                    $itemsToInsert = [];
                    $lastPosition = $packProduct->selectedVariations()->count(); // Get the current count for new positions

                    foreach ($uniqueIdsToCreate as $index => $uniqueId) {
                        [$type, $id] = explode('-', $uniqueId);
                        $itemData = [
                            'pack_product_id' => $packProduct->id,
                            'product_id' => null,
                            'product_variation_id' => null,
                        ];

                        if ($type === 'p') {
                            $itemData['product_id'] = $id;
                        } elseif ($type === 'v') {
                            $variation = \App\Models\ProductVariation::find($id);
                            if ($variation) {
                                $itemData['product_id'] = $variation->product_id;
                                $itemData['product_variation_id'] = $id;
                            }
                        }

                        if ($itemData['product_id'] !== null) {
                            $itemsToInsert[] = $itemData;
                        }
                    }

                    if (!empty($itemsToInsert)) {
                        PackProductSelectedVariation::insert($itemsToInsert);
                    }
                }
            });
        } catch (\Exception $e) {
            \Log::error("Error in saveSelectedItems: " . $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getFile());
            return response()->json(['success' => false, 'message' => 'A server error occurred.'], 500);
        }

        return response()->json(['success' => true, 'count' => count($request->input('item_ids', []))]);
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
