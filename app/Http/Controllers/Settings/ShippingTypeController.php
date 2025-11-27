<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ShippingType;
use Illuminate\Http\Request;

class ShippingTypeController extends Controller
{
    public function index()
    {
        $types = ShippingType::orderBy('name')->paginate(20);
        return view('settings.shipping_types.index', compact('types'));
    }

    public function create()
    {
        return view('settings.shipping_types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:shipping_types,name',
        ]);

        ShippingType::create($data);

        return redirect()->route('shipping-types.index')->with('success', 'Shipping type created.');
    }

    public function edit(ShippingType $shipping_type)
    {
        return view('settings.shipping_types.edit', ['type' => $shipping_type]);
    }

    public function update(Request $request, ShippingType $shipping_type)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:shipping_types,name,' . $shipping_type->id,
        ]);

        $shipping_type->update($data);

        return redirect()->route('shipping-types.index')->with('success', 'Shipping type updated.');
    }

    public function destroy(ShippingType $shipping_type)
    {
        $shipping_type->delete();
        return redirect()->route('shipping-types.index')->with('success', 'Shipping type deleted.');
    }
}
