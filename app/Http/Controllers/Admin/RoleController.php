<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::where('name', '!=', 'Super Admin')->latest()->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create($request->all());

        return redirect()->route('admin.roles.index')
                         ->with('success', 'Role created successfully.');
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
    public function edit(Role $role)
    {
        // SECURITY: Prevent editing of the Super Admin role.
        if ($role->name === 'Super Admin') {
            abort(403, 'You cannot edit the Super Admin role.');
        }

        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // SECURITY: Prevent editing of the Super Admin role.
        if ($role->name === 'Super Admin') {
            abort(403, 'You cannot edit the Super Admin role.');
        }

        $request->validate([
            // The unique rule needs to ignore the current role's ID
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        $role->update($request->all());

        return redirect()->route('admin.roles.index')
                         ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // SECURITY: Prevent deleting the Super Admin role.
        if ($role->name === 'Super Admin') {
            return back()->with('error', 'You cannot delete the Super Admin role.');
        }

        // Optional: Prevent deleting roles that have users assigned to them.
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete a role that has assigned users.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
                         ->with('success', 'Role deleted successfully.');
    }
}
