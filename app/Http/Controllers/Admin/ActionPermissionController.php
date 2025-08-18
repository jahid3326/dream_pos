<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class ActionPermissionController extends Controller
{
    // Define which models have granular C/R/U/D permissions
    protected $manageableModels = [
        // 'Student',
        'Customer',
        'Supplier',
        'Category',
        'Product',
        'Tax',

    ];

    public function index()
    {
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('admin.action-permissions.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        $permissions = $role->permissions->keyBy('model_name');
        return view('admin.action-permissions.edit', [
            'role' => $role,
            'models' => $this->manageableModels,
            'currentPermissions' => $permissions
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $submitted = $request->input('permissions', []);

        foreach ($this->manageableModels as $model_name) {
            $data = $submitted[$model_name] ?? [];

            Permission::updateOrCreate(
                ['role_id' => $role->id, 'model_name' => $model_name],
                [
                    'can_create' => isset($data['create']),
                    'can_read'   => isset($data['read']),
                    'can_show'   => isset($data['show']), // <-- ADD THIS
                    'can_update' => isset($data['update']),
                    'can_delete' => isset($data['delete']),
                ]
            );
        }

        return redirect()->route('admin.action-permissions.index')
            ->with('success', 'Action permissions updated successfully!');
    }
}
