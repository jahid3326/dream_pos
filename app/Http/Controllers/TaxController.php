<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tax;
use Illuminate\Support\Facades\Validator;

class TaxController extends Controller
{

    public function __construct()
    {
        $this->middleware('action.permission:Tax,read')->only('index');
        $this->middleware('action.permission:Tax,create')->only(['create', 'store']);
        $this->middleware('action.permission:Tax,show')->only('show');
        $this->middleware('action.permission:Tax,update')->only(['edit', 'update']);
        $this->middleware('action.permission:Tax,delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxes = Tax::latest()->get();
        return view('taxes.index', compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('taxes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:taxes,name',
            'rate' => 'required|numeric|min:0|max:100',
        ]);
        Tax::create($request->all());
        return redirect()->route('taxes.index')->with('success', 'Tax created successfully.');
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
    public function edit(Tax $tax)
    {
        return view('taxes.edit', compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:taxes,name,' . $tax->id,
            'rate' => 'required|numeric|min:0|max:100',
        ]);
        $tax->update($request->all());
        return redirect()->route('taxes.index')->with('success', 'Tax updated successfully.');
    }

    public function ajaxStore(Request $request)
    {
        // Use manual validation to return JSON errors
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:taxes,name',
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ], 422); // 422 Unprocessable Entity
        }

        $tax = Tax::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tax created successfully!',
            'tax' => $tax // Send the newly created tax back to the frontend
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        $tax->delete();
        return redirect()->route('taxes.index')->with('success', 'Tax deleted successfully.');
    }
}
