<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $model_name, string $action): Response
    {
        $user = Auth::user();

        // Super Admin bypasses this check
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $permission = Permission::where('role_id', $user->role_id)
                                ->where('model_name', $model_name)
                                ->first();

        $permission_column = 'can_' . $action; // e.g., 'can_create', 'can_read'

        if ($permission && $permission->$permission_column) {
            return $next($request);
        }

        abort(403, 'You do not have permission to perform this action.');
    }
}
