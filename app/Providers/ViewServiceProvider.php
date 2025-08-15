<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Using a view composer to share data with specific views
        View::composer('layouts.app', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                
                if ($user->isSuperAdmin()) {
                    // Super Admin gets all nav items, structured hierarchically
                    $navItems = \App\Models\NavItem::whereNull('parent_id')
                                    ->with('children')
                                    ->orderBy('order')
                                    ->get();
                } else {
                    // Other users get only the nav items assigned to their role
                    $navItems = $user->role->navItems()
                                    ->whereNull('parent_id')
                                    ->with('children')
                                    ->orderBy('order')
                                    ->get();
                }

                $view->with('navItems', $navItems);
            }
        });
    }
}
