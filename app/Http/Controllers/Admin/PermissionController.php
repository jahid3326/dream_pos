<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\NavItem;

class PermissionController extends Controller
{
    protected $manageableModules = [
        'System Management' => [
            'Role',
            'User',
            'NavItem' // Model for managing Navigation Items
        ],
        'School Management' => [
            'Student', // Example Model
            'Class',   // Example Model
            'Subject', // Example Model
            'Teacher', // Example Model
        ],
        // Add other groups and modules here as your application grows
    ];

    /**
     * Display a list of roles to manage permissions for.
     */
    public function index()
    {
        // Get all roles except 'Super Admin' because they have all permissions by default.
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('admin.permissions.index', compact('roles'));
    }

    /**
     * Show the form for editing permissions for a specific role.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\View\View
     */
    public function edit(Role $role)
    {
        $allNavItems = NavItem::whereNull('parent_id')
                            ->with(['children' => function ($query) {
                                $query->orderBy('order');
                            }, 'children.children' => function ($query) {
                                $query->orderBy('order');
                            }])
                            ->orderBy('order')
                            ->get();
                            
        // Get the IDs of the nav items currently assigned to this role for checking the boxes.
        $assignedNavIds = $role->navItems()->pluck('nav_items.id')->toArray();

        return view('admin.permissions.edit', [
            'role' => $role,
            'allNavItems' => $allNavItems,
            'assignedNavIds' => $assignedNavIds
        ]);
    }

    /**
     * Update the permissions for the given role in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Role $role)
    {
        $navItemIds = $request->input('nav_item_ids', []);
        $role->navItems()->sync($navItemIds);

        return redirect()->route('admin.permissions.index')
                         ->with('success', 'Navigation permissions updated successfully!');
    }
}
