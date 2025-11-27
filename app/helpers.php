<?php

use Illuminate\Support\Facades\Auth;
use App\Models\Permission;

if (!function_exists('hasActionPermission')) {
    /**
     * Check if the authenticated user has a specific action permission.
     *
     * @param string $modelName The name of the model (e.g., 'Student').
     * @param string $action The action to check (e.g., 'create', 'read').
     * @return bool
     */
    function hasActionPermission(string $modelName, string $action): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $permission = Permission::where('role_id', $user->role_id)
                                ->where('model_name', $modelName)
                                ->first();

        $permissionColumn = 'can_' . $action;

        return $permission && $permission->$permissionColumn;
    }
}