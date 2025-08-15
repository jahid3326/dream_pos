<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade; // <-- Import the Blade facade
use Illuminate\Support\Facades\Auth;   // <-- Import the Auth facade
use App\Models\Permission;              // <-- Import the Permission model

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define the custom Blade directive: @actionPermission('ModelName', 'action')
        Blade::if('actionPermission', function (string $modelName, string $action) {
            // 1. Get the currently authenticated user
            $user = Auth::user();

            // 2. If no user is logged in, they have no permission
            if (!$user) {
                return false;
            }

            // 3. The Super Admin always has permission
            if ($user->isSuperAdmin()) {
                return true;
            }

            // 4. Check the permissions table for other roles
            $permission = Permission::where('role_id', $user->role_id)
                                    ->where('model_name', $modelName)
                                    ->first();

            // 5. Determine the permission column to check (e.g., 'can_create')
            $permissionColumn = 'can_' . $action;

            // 6. Return true if the permission exists and is set to true, otherwise false
            return $permission && $permission->$permissionColumn;
        });
    }
}
