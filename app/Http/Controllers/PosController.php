<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ProductVariation;
use App\Models\Tax;

class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch top-level categories
        $categories = Category::whereNotNull('parent_id')->orderBy('name')->get();

        $allProducts = Product::with('variations', 'tax')->where('type', 'single')->get();
        $allVariations = ProductVariation::with('product.category', 'tax')->get();

        $displayProducts = collect();
        foreach ($allProducts as $product) {
            $displayProducts->push([
                'id' => $product->id,
                'variation_id' => null,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->sale_price,
                'image' => $product->product_image ? asset('public/storage/' . $product->product_image) : asset('public/storage/images/default_image.png'),
                'tax_rate' => $product->tax->rate ?? 0,
                'category_id' => $product->category_id,
            ]);
        }
        foreach ($allVariations as $variation) {
            $displayProducts->push([
                'id' => $variation->product->id,
                'variation_id' => $variation->id,
                'name' => "{$variation->product->name} - {$variation->measurement}",
                'sku' => $variation->sku,
                'price' => $variation->sale_price,
                'image' => $variation->image ? asset('public/storage/' . $variation->image) : asset('public/storage/images/default_image.png'),
                'tax_rate' => $variation->tax->rate ?? 0,
                'category_id' => $variation->product->category_id,
            ]);
        }

        $customers = Customer::with('user')->get();
        $taxes = Tax::where('status', true)->get();

        return view('pos.index', [
            'categories' => $categories,
            'products' => $displayProducts,
            'customers' => $customers,
            'taxes' => $taxes
        ]);
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
