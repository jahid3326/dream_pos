<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckNavPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. Super Admin can access everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // 2. Get the name of the current route
        $currentRouteName = $request->route()->getName();
        
        // Allow access to the dashboard by default
        if ($currentRouteName === 'dashboard') {
            return $next($request);
        }

        // 3. Get all the route names the user's role has access to
        $allowedRoutes = $user->role->navItems()->pluck('route')->filter()->toArray();

        // 4. Check for an exact match (for top-level menu items)
        if (in_array($currentRouteName, $allowedRoutes)) {
            return $next($request);
        }

        // 5. SMART CHECK: Check if the current route belongs to an allowed module
        // e.g., if user can access 'students.index', they should also pass this check for 'students.create'
        $currentRouteParts = explode('.', $currentRouteName);
        $currentModule = $currentRouteParts[0] ?? null; // e.g., 'students'

        foreach ($allowedRoutes as $allowedRoute) {
            if (str_starts_with($allowedRoute, $currentModule . '.')) {
                // The user has access to at least one route in this module,
                // so let the request pass to the next middleware (CheckActionPermission).
                return $next($request);
            }
        }

        // 6. If no permission, deny access
        abort(403, 'You do not have permission to access this page.');
    }
}
