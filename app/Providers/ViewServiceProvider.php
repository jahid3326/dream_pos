<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\NavItem;

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
            $navItemsForView = collect();

            if (Auth::check()) {
                $user = Auth::user();

                // Handle Super Admin separately - they see everything.
                if ($user->role && $user->role->name === 'Super Admin') {
                    $navItemsForView = NavItem::whereNull('parent_id')
                        ->with(['children' => fn($q) => $q->orderBy('order'), 'children.children' => fn($q) => $q->orderBy('order')])
                        ->orderBy('order')
                        ->get();
                }
                // Handle other roles
                elseif ($user->role) {
                    // Get the IDs of nav items this user's role has permission for.
                    $allowedNavIds = $user->role->navItems()->pluck('nav_items.id')->toArray();

                    // Fetch the top-level items (Headers) and their children,
                    // but only if the children/grandchildren are in the allowed list.
                    $navItemsForView = NavItem::whereNull('parent_id')
                        ->with([
                            'children' => function ($query) use ($allowedNavIds) {
                                $query->whereIn('id', $allowedNavIds) // Filter direct links
                                    ->orWhereHas('children', function ($subQuery) use ($allowedNavIds) {
                                        $subQuery->whereIn('id', $allowedNavIds); // Filter children of dropdowns
                                    })->orderBy('order');
                            },
                            // We also need to load the grandchildren for the dropdowns
                            'children.children' => function ($query) use ($allowedNavIds) {
                                $query->whereIn('id', $allowedNavIds)->orderBy('order');
                            }
                        ])
                        ->whereHas('children', function ($query) use ($allowedNavIds) {
                            // A header is only shown if it has a permitted child or grandchild
                            $query->whereIn('id', $allowedNavIds)
                                ->orWhereHas('children', function ($subQuery) use ($allowedNavIds) {
                                    $subQuery->whereIn('id', $allowedNavIds);
                                });
                        })
                        ->orderBy('order')
                        ->get();
                }

                $view->with('navItems', $navItemsForView);
            }
            /*
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
            */
        });
    }
}
