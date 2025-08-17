<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Tax;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ProductVariation;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('action.permission:Product,read')->only('index');
        $this->middleware('action.permission:Product,create')->only(['create', 'store']);
        $this->middleware('action.permission:Product,show')->only('show');
        $this->middleware('action.permission:Product,update')->only(['edit', 'update']);
        $this->middleware('action.permission:Product,delete')->only('destroy');
        $this->middleware('action.permission:Product,delete')->only('destroyVariation');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier', 'variations']);

        // 2. Apply filters based on request input
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        // if ($request->filled('category_id')) {
        //     $query->where('category_id', $request->category_id);
        // }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhereHas('variations', function ($varQuery) use ($search) {
                        $varQuery->where('sku', 'like', '%' . $search . '%');
                    });
            });
        }

        // --- NEW AND IMPROVED CATEGORY FILTER LOGIC ---
        if ($request->filled('category_id')) {
            $categoryId = $request->category_id;

            // Find the selected category and its children
            $selectedCategory = Category::with('children')->find($categoryId);

            if ($selectedCategory) {
                // Check if the selected category is a parent with children
                if ($selectedCategory->children->isNotEmpty()) {
                    // Get an array of all child IDs
                    $categoryIds = $selectedCategory->children->pluck('id');
                    // Add the parent category's ID to the array
                    $categoryIds->push($selectedCategory->id);

                    // Use whereIn to find products in the parent OR any of its children
                    $query->whereIn('category_id', $categoryIds);
                } else {
                    // If it's a child category (or a parent with no children), just filter by its ID
                    $query->where('category_id', $categoryId);
                }
            }
        }


        // 3. Fetch the products from the database
        $products = $query->latest()->get();

        // 4. Transform the fetched products into a flattened list for the view
        $product_list = collect(); // Create a new empty Laravel Collection

        foreach ($products as $product) {
            if ($product->type === 'single') {
                // For single products, create a standardized object
                $row = (object) [
                    'id' => $product->id,
                    'variation_id' => null, // Single products have no variation ID
                    'name' => $product->name,
                    'measurement' => $product->measurement,
                    'is_variation' => false,
                    'category' => $product->category,
                    'supplier' => $product->supplier,
                    'sale_price' => $product->sale_price,
                    'purchase_price' => $product->purchase_price,
                ];
                $product_list->push($row);
            } elseif ($product->type === 'variation') {
                if ($product->variations->isNotEmpty()) {
                    // If it's a variation product with variations, loop through them
                    foreach ($product->variations as $variation) {
                        $row = (object) [
                            'id' => $product->id, // Parent product ID
                            'variation_id' => $variation->id, // The specific variation ID
                            'name' => $product->name,
                            'measurement' => $variation->measurement,
                            'is_variation' => true,
                            'category' => $product->category,
                            'supplier' => $product->supplier,
                            'sale_price' => $variation->sale_price,
                            'purchase_price' => $variation->purchase_price,
                        ];
                        $product_list->push($row);
                    }
                } else {
                    // Handle the edge case of a "variation" product with no actual variations yet
                    $row = (object) [
                        'id' => $product->id,
                        'variation_id' => null,
                        'name' => $product->name,
                        'measurement' => 'No variations added',
                        'is_variation' => true, // Still true by type
                        'category' => $product->category,
                        'supplier' => $product->supplier,
                        'sale_price' => 0.00,
                        'purchase_price' => 0.00,
                    ];
                    $product_list->push($row);
                }
            }
        }

        // 5. Get data for the filter dropdowns
        $suppliers = Supplier::with('user')->get();
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        // 6. Return the view with the transformed data
        // Note: We don't use Laravel's paginator because we built a custom collection.
        // Client-side pagination (e.g., via DataTables) is expected.
        return view('products.index', compact('product_list', 'suppliers', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::with('user')->get();
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $taxes = Tax::where('status', true)->get();

        // Pass an empty product object to the form for consistency
        $product = new Product();

        return view('products.create', compact('product', 'suppliers', 'categories', 'taxes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['type' => 'required|in:single,variation']);

        DB::transaction(function () use ($request) {

            // --- SINGLE PRODUCT LOGIC ---
            if ($request->type === 'single') {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'supplier_id' => 'required|exists:suppliers,id',
                    'category_id' => 'required|exists:categories,id',
                    'sku' => 'required|string|unique:products,sku',
                    'purchase_price' => 'required|numeric|min:0',
                    'sale_price' => 'required|numeric|min:0',
                    'margin' => 'nullable|numeric|min:0|max:100',
                    'tax_id' => 'nullable|exists:taxes,id',
                    'measurement' => 'nullable|string',
                    'cbm' => 'nullable|numeric',
                    'weight' => 'nullable|numeric',
                    'product_image' => 'nullable|image|max:2048',
                ]);

                $data = $validated + ['type' => 'single'];

                if ($request->hasFile('product_image')) {
                    $data['product_image'] = $request->file('product_image')->store('product_images', 'public');
                }

                Product::create($data);
            }

            // --- VARIATION PRODUCT LOGIC ---
            elseif ($request->type === 'variation') {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'supplier_id' => 'required|exists:suppliers,id',
                    'category_id' => 'required|exists:categories,id',
                    'variations' => 'required|array|min:1',
                    'variations.*.sku' => 'required|string|unique:product_variations,sku',
                    'variations.*.purchase_price' => 'required|numeric|min:0',
                    'variations.*.sale_price' => 'required|numeric|min:0',
                    'variations.*.margin' => 'nullable|numeric|min:0|max:100',
                    'variations.*.tax_id' => 'nullable|exists:taxes,id',
                    'variations.*.image' => 'nullable|image|max:2048',
                    'variations.*.measurement' => 'nullable|string', // <-- ADD VALIDATION
                    'variations.*.cbm' => 'nullable|numeric',       // <-- ADD VALIDATION
                    'variations.*.weight' => 'nullable|numeric',     // <-- ADD VALIDATION
                ]);

                // Create the parent product record
                $product = Product::create([
                    'name' => $validated['name'],
                    'type' => 'variation',
                    'supplier_id' => $validated['supplier_id'],
                    'category_id' => $validated['category_id'],
                ]);

                // Loop through and create each variation
                foreach ($validated['variations'] as $index => $variationData) {
                    if ($request->hasFile("variations.{$index}.image")) {
                        $variationData['image'] = $request->file("variations.{$index}.image")
                            ->store('variation_images', 'public');
                    }
                    // The $variationData array now correctly contains all fields
                    // thanks to the validation step above.
                    $product->variations()->create($variationData);
                }
            }
        });

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function showImportForm()
    {
        return view('products.import');
    }

    public function import(Request $request)
    {
        $request->validate(['product_file' => 'required|mimes:xlsx,csv']);
        $import = new ProductImport;
        Excel::import($import, $request->file('product_file'));

        if (!empty($import->errors)) {
            return redirect()->route('products.import.show')
                ->with('import_errors', $import->errors)
                ->with('error', "Import finished with errors. {$import->importedCount} parent products were imported.");
        }
        return redirect()->route('products.index')->with('success', "All {$import->importedCount} products imported successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'supplier.user', 'variations.tax', 'tax']);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Eager load relationships for the edit form
        $product->load('variations');

        $suppliers = Supplier::with('user')->get();
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $taxes = Tax::where('status', true)->get();

        return view('products.edit', compact('product', 'suppliers', 'categories', 'taxes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate(['type' => 'required|in:single,variation']);

        DB::transaction(function () use ($request, $product) {

            // Handle switching product types
            if ($product->type === 'variation' && $request->type === 'single') {
                $product->variations()->delete(); // Delete old variations
            }
            if ($product->type === 'single' && $request->type === 'variation') {
                // Clear old single product data
                $product->update(['sku' => null, 'purchase_price' => null, 'sale_price' => null, 'margin' => null, 'tax_id' => null, 'measurement' => null, 'cbm' => null, 'weight' => null]);
            }

            // --- SINGLE PRODUCT LOGIC ---
            if ($request->type === 'single') {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'supplier_id' => 'required|exists:suppliers,id',
                    'category_id' => 'required|exists:categories,id',
                    'sku' => 'required|string|unique:products,sku,' . $product->id,
                    'purchase_price' => 'required|numeric|min:0',
                    'sale_price' => 'required|numeric|min:0',
                    'margin' => 'nullable|numeric|min:0|max:100',
                    'tax_id' => 'nullable|exists:taxes,id',
                    'measurement' => 'nullable|string',
                    'cbm' => 'nullable|numeric',
                    'weight' => 'nullable|numeric',
                    'product_image' => 'nullable|image|max:2048',
                ]);

                $data = $validated + ['type' => 'single'];

                if ($request->hasFile('product_image')) {
                    if ($product->product_image) Storage::disk('public')->delete($product->product_image);
                    $data['product_image'] = $request->file('product_image')->store('product_images', 'public');
                }

                $product->update($data);
            }

            // --- VARIATION PRODUCT LOGIC ---
            elseif ($request->type === 'variation') {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'supplier_id' => 'required|exists:suppliers,id',
                    'category_id' => 'required|exists:categories,id',
                    'variations' => 'required|array|min:1',
                    'variations.*.id' => 'nullable|exists:product_variations,id',
                    'variations.*.sku' => 'required|string',
                    'variations.*.purchase_price' => 'required|numeric|min:0',
                    'variations.*.sale_price' => 'required|numeric|min:0',
                    'variations.*.margin' => 'nullable|numeric|min:0|max:100',
                    'variations.*.tax_id' => 'nullable|exists:taxes,id',
                    'variations.*.image' => 'nullable|image|max:2048',
                    'variations.*.measurement' => 'nullable|string', // <-- ADD VALIDATION
                    'variations.*.cbm' => 'nullable|numeric',       // <-- ADD VALIDATION
                    'variations.*.weight' => 'nullable|numeric',     // <-- ADD VALIDATION
                ]);

                // First, update the parent product
                $product->update([
                    'name' => $validated['name'],
                    'type' => 'variation',
                    'supplier_id' => $validated['supplier_id'],
                    'category_id' => $validated['category_id'],
                ]);

                $submittedVariationIds = [];

                foreach ($validated['variations'] as $index => $variationData) {
                    $variationId = $variationData['id'] ?? null;

                    if ($request->hasFile("variations.{$index}.image")) {
                        // If there's an existing image for this variation, delete it
                        if ($variationId && ($variation = $product->variations()->find($variationId)) && $variation->image) {
                            Storage::disk('public')->delete($variation->image);
                        }
                        $variationData['image'] = $request->file("variations.{$index}.image")->store('variation_images', 'public');
                    }

                    // Use updateOrCreate to handle both existing and new variations
                    $variation = $product->variations()->updateOrCreate(
                        ['id' => $variationData['id'] ?? null], // Conditions to find
                        $variationData                          // Data to update/create
                    );
                    $submittedVariationIds[] = $variation->id;
                }

                // Delete any variations that were removed by the user
                $product->variations()->whereNotIn('id', $submittedVariationIds)->delete();
            }
        });

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Remove a specific product variation from storage.
     *
     * @param  \App\Models\ProductVariation  $variation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyVariation(ProductVariation $variation)
    {
        // Optional: Authorize the action.
        // We can check if the user has 'delete' permission for the parent Product.

        // Also check if this is the last variation.
        // You might want to prevent deleting the last one to avoid an empty variation product.
        $parentProduct = $variation->product;
        if ($parentProduct->variations()->count() === 1) {
            // This is the LAST variation, so delete the entire parent product.
            // The database cascade will handle deleting this final variation.

            // Delete the parent product's main image if it has one
            if ($parentProduct->product_image) {
                Storage::disk('public')->delete($parentProduct->product_image);
            }

            // Delete the parent product
            $parentProduct->delete();

            return redirect()->route('products.index')->with('success', 'The last variation was deleted, and the parent product has been removed.');
        } else {
            // There are other variations, so ONLY delete this specific one.

            // Delete the variation's image from storage if it exists
            if ($variation->image) {
                Storage::disk('public')->delete($variation->image);
            }

            // Delete the variation record from the database
            $variation->delete();

            return back()->with('success', 'Product variation deleted successfully.');
        }
    }
}
