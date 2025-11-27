<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ShippingTax;
use Illuminate\Http\Request;

class ShippingTaxController extends Controller
{
    public function index()
    {
        $items = ShippingTax::orderBy('name')->paginate(20);
        return view('settings.shipping_taxes.index', compact('items'));
    }

    public function create()
    {
        return view('settings.shipping_taxes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:shipping_taxes,name',
        ]);

        ShippingTax::create($data);

        return redirect()->route('shipping-taxes.index')->with('success', 'Shipping tax created.');
    }

    public function edit(ShippingTax $shipping_tax)
    {
        return view('settings.shipping_taxes.edit', ['item' => $shipping_tax]);
    }

    public function update(Request $request, ShippingTax $shipping_tax)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:shipping_taxes,name,' . $shipping_tax->id,
        ]);

        $shipping_tax->update($data);

        return redirect()->route('shipping-taxes.index')->with('success', 'Shipping tax updated.');
    }

    public function destroy(ShippingTax $shipping_tax)
    {
        $shipping_tax->delete();
        return redirect()->route('shipping-taxes.index')->with('success', 'Shipping tax deleted.');
    }
}
