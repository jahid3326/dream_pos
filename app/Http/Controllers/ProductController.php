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

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('action.permission:Product,read')->only('index');
        $this->middleware('action.permission:Product,create')->only(['create', 'store']);
        $this->middleware('action.permission:Product,show')->only('show');
        $this->middleware('action.permission:Product,update')->only(['edit', 'update']);
        $this->middleware('action.permission:Product,delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier', 'variations']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->get();
        $suppliers = Supplier::with('user')->get();
        $categories = Category::whereNull('parent_id')->get();

        return view('products.index', compact('products', 'suppliers', 'categories'));
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
                    'variations.*.image' => 'nullable|image|max:2048',
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
                    $product->variations()->create($variationData);
                }
            }
        });

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
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
                    'variations.*.sku' => 'required|string', // Unique validation is complex, best handled with a custom rule or DB constraint
                    'variations.*.purchase_price' => 'required|numeric|min:0',
                    'variations.*.sale_price' => 'required|numeric|min:0',
                    'variations.*.margin' => 'nullable|numeric|min:0|max:100',
                    'variations.*.image' => 'nullable|image|max:2048',
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
                        ['id' => $variationId], // Conditions to find the record
                        $variationData           // Data to update or create
                    );
                    $submittedVariationIds[] = $variation->id;
                }

                // Delete any variations that were on the form originally but were removed by the user
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
}
