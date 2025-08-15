<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\NavItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Use Storage facade

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // === Part 1: Create Super Admin Role and User ===
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        User::firstOrCreate(
            ['email' => 'admin@pos.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'role_id' => $superAdminRole->id,
                'profile_picture' => 'images/default_avatar.png'
            ]
        );
        
        // === Part 2: Create the Default Navigation Items for the Admin ===

        // 1. Create the "Main Menu" Header
        $mainMenuHeader = NavItem::firstOrCreate(
            ['name' => 'Main Menu'],
            [
                'type' => 'header',
                'order' => 1,
            ]
        );

        // Make Dashboard a child of the Main Menu header
        NavItem::updateOrCreate(
            ['route' => 'dashboard'],
            [
                'name' => 'Dashboard',
                'type' => 'link',
                'icon' => 'ti-layout-grid',
                'parent_id' => $mainMenuHeader->id,
                'order' => 10,
            ]
        );

        // Make Students a child of the Main Menu header
        NavItem::updateOrCreate(
            ['route' => 'students.index'],
            [
                'name' => 'Students',
                'type' => 'link',
                'icon' => 'fa-user-graduate',
                'parent_id' => $mainMenuHeader->id,
                'order' => 20,
            ]
        );

        // 2. Create the "Settings" Header
        $settingsHeader = NavItem::firstOrCreate(
            ['name' => 'System Settings'],
            [
                'type' => 'header',
                'order' => 100,
            ]
        );
        
        // Make the original "Settings" dropdown a child of the new header
        $settingsDropdown = NavItem::updateOrCreate(
            ['name' => 'Settings'],
            [
                'type' => 'dropdown',
                'route' => null, // A dropdown trigger has no route
                'icon' => 'fa-cogs',
                'parent_id' => $settingsHeader->id,
                'order' => 110
            ]
        );
        
        // Make all settings links children of the "Settings" DROPDOWN
        NavItem::updateOrCreate(['route' => 'admin.permissions.index'], ['name' => 'Navigation Permissions', 'type' => 'link', 'parent_id' => $settingsDropdown->id, 'order' => 1]);
        NavItem::updateOrCreate(['route' => 'admin.action-permissions.index'], ['name' => 'Action Permissions', 'type' => 'link', 'parent_id' => $settingsDropdown->id, 'order' => 2]);
        NavItem::updateOrCreate(['route' => 'admin.roles.index'], ['name' => 'Roles', 'type' => 'link', 'parent_id' => $settingsDropdown->id, 'order' => 3]);
        NavItem::updateOrCreate(['route' => 'admin.users.index'], ['name' => 'Users', 'type' => 'link', 'parent_id' => $settingsDropdown->id, 'order' => 4]);
        NavItem::updateOrCreate(['route' => 'admin.nav-items.index'], ['name' => 'Navigation Menu', 'type' => 'link', 'parent_id' => $settingsDropdown->id, 'order' => 5]);

        // // Create Dashboard (if you don't have one)
        // $dashboardNav = NavItem::firstOrCreate(
        //     ['name' => 'Dashboard'],
        //     [
        //         'route' => 'dashboard',
        //         'icon' => 'ti-layout-grid fs-16',
        //         'parent_id' => null,
        //         'order' => 1
        //     ]
        // );

        // $salesNav = NavItem::firstOrCreate(
        //     ['name' => 'Sales'],
        //     [
        //         'route' => null,
        //         'icon' => 'ti-layout-grid fs-16',
        //         'parent_id' => null,
        //         'order' => 10
        //     ]
        // );

        // $onlineOrdersNav = NavItem::firstOrCreate(
        //     ['route' => 'onlineorder'], // Find by route
        //     [
        //         'name' => 'Online Orders', // Update name
        //         'icon' => '',
        //         'parent_id' => $salesNav->id,
        //         'order' => 1
        //     ]
        // );

        // $posOrdersNav = NavItem::firstOrCreate(
        //     ['route' => 'posorder'], // Find by route
        //     [
        //         'name' => 'POS Orders', // Update name
        //         'icon' => '',
        //         'parent_id' => $salesNav->id,
        //         'order' => 2
        //     ]
        // );

        
        // $settingsNav = NavItem::firstOrCreate(
        //     ['name' => 'Settings'],
        //     [
        //         'route' => null,
        //         'icon' => 'fa-cogs',
        //         'parent_id' => null,
        //         'order' => 100
        //     ]
        // );

        // // Create/Update the child navigation items with a logical order
        // $navPermissionNav = NavItem::firstOrCreate(
        //     ['route' => 'admin.permissions.index'], // Find by route
        //     [
        //         'name' => 'Navigation Permissions', // Update name
        //         'icon' => 'fa-list-check',
        //         'parent_id' => $settingsNav->id,
        //         'order' => 1
        //     ]
        // );

        // // Add the new Action Permissions nav item
        // $actionPermissionNav = NavItem::firstOrCreate(
        //     ['name' => 'Action Permissions'],
        //     [
        //         'route' => 'admin.action-permissions.index',
        //         'icon' => 'fa-shield-halved',
        //         'parent_id' => $settingsNav->id,
        //         'order' => 2 // Place it after Nav Permissions
        //     ]
        // );

        // $roleNav = NavItem::firstOrCreate(
        //     ['name' => 'Roles'],
        //     [
        //         'route' => 'admin.roles.index', // CORRECT
        //         'icon' => 'fa-users',
        //         'parent_id' => $settingsNav->id,
        //         'order' => 2
        //     ]
        // );

        // $userNav = NavItem::firstOrCreate(
        //     ['name' => 'Users'],
        //     [
        //         'route' => 'admin.users.index', // CORRECT
        //         'icon' => 'fa-user-cog',
        //         'parent_id' => $settingsNav->id,
        //         'order' => 3
        //     ]
        // );

        // $navItemNav = NavItem::firstOrCreate(
        //     ['name' => 'Navigation'],
        //     [
        //         'route' => 'admin.nav-items.index', // CORRECT
        //         'icon' => 'fa-bars',
        //         'parent_id' => $settingsNav->id,
        //         'order' => 4
        //     ]
        // );

        // // === Part 3: Link All Nav Items to Super Admin Role ===
        // $superAdminRole->navItems()->syncWithoutDetaching([
        //     $dashboardNav->id,
        //     $salesNav->id,
        //     $onlineOrdersNav->id,
        //     $posOrdersNav->id,
        //     $settingsNav->id,
        //     $navPermissionNav->id,
        //     $actionPermissionNav->id,
        //     $roleNav->id,
        //     $userNav->id,
        //     $navItemNav->id
        // ]);
    }
}
