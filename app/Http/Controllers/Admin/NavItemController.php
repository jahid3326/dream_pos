<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NavItem;

class NavItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $navItems = NavItem::whereNull('parent_id')
                            ->with('children')
                            ->orderBy('order', 'asc')
                            ->get();
        return view('admin.nav-items.index', compact('navItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parents = NavItem::whereIn('type', ['header', 'dropdown'])
                            ->orderBy('name')
                            ->get();
        return view('admin.nav-items.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:header,link,dropdown', // Validate the type
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'order' => 'required|integer',
            // If the type is 'header', parent_id must be null.
            'parent_id' => 'nullable|exists:nav_items,id|required_if:type,link|required_if:type,dropdown',
        ], [
            // Custom error messages for clarity
            'parent_id.required_if' => 'A parent item is required for Link and Dropdown types.',
        ]);

        // If type is header, force parent_id to null
        $data = $request->all();
        if ($data['type'] === 'header') {
            $data['parent_id'] = null;
        }

        NavItem::create($data);

        return redirect()->route('admin.nav-items.index')->with('success', 'Navigation item created successfully.');
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
    public function edit(NavItem $navItem)
    {
        // Get potential parents, excluding the current item and its children.
        $parents = NavItem::whereIn('type', ['header', 'dropdown'])
                            ->where('id', '!=', $navItem->id)
                            ->orderBy('name')
                            ->get();
        return view('admin.nav-items.edit', compact('navItem', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NavItem $navItem)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:header,link,dropdown',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'order' => 'required|integer',
            'parent_id' => 'nullable|exists:nav_items,id|required_if:type,link|required_if:type,dropdown',
        ], [
            'parent_id.required_if' => 'A parent item is required for Link and Dropdown types.',
        ]);
        
        $data = $request->all();
        if ($data['type'] === 'header') {
            $data['parent_id'] = null;
        }

        if ($request->parent_id == $navItem->id) {
             return back()->with('error', 'A navigation item cannot be its own parent.');
        }

        $navItem->update($data);

        return redirect()->route('admin.nav-items.index')->with('success', 'Navigation item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NavItem $navItem)
    {
        $navItem->delete();
        return redirect()->route('admin.nav-items.index')->with('success', 'Navigation item and its children deleted successfully.');
    }
}
