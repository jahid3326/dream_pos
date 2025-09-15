<?php

use App\Http\Controllers\Admin\ActionPermissionController;
use App\Http\Controllers\Admin\NavItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ManagePackController;
use App\Http\Controllers\PackController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaxController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
    return 'Storage link has been created.';
});
// Authentication Routes

// GUEST ROUTES: Only accessible if the user is NOT logged in.
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
// Route::post('login', [AuthController::class, 'login']);
// Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // All other routes for your app will go here...
});


// Redirect root to login if not authenticated, or dashboard if authenticated
Route::get('/', function () {
    // This check handles both cases elegantly
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    // Logout route for all authenticated users
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // ... dashboard and other authenticated routes

    // Dashboard for all authenticated users
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Super Admin Routes
    Route::middleware(['auth'])->group(function () {

        // Add a check here to ensure only Super Admin can access these routes
        // This is a simple inline middleware for demonstration

        // --- 1. GENERAL APPLICATION ROUTES (Permission controlled by 'nav.permission') ---
        // These routes are accessible to any role that has been granted permission by the Super Admin.

        Route::middleware(['nav.permission'])->group(function () {

            // Student-related routes
            Route::get('students', [StudentController::class, 'index'])
                ->name('students.index')->middleware('action.permission:Student,read');

            Route::get('students/create', [StudentController::class, 'create'])
                ->name('students.create')->middleware('action.permission:Student,create');

            Route::post('students', [StudentController::class, 'store'])
                ->name('students.store')->middleware('action.permission:Student,create');

            Route::get('students/{student}/edit', [StudentController::class, 'edit'])
                ->name('students.edit')->middleware('action.permission:Student,update');

            Route::put('students/{student}', [StudentController::class, 'update'])
                ->name('students.update')->middleware('action.permission:Student,update');

            Route::delete('students/{student}', [StudentController::class, 'destroy'])
                ->name('students.destroy')->middleware('action.permission:Student,delete');


            // Teacher-related routes
            Route::get('teachers', [StudentController::class, 'index'])
                ->name('teachers.index')->middleware('action.permission:Teacher,read');

            Route::get('onlineorder', [StudentController::class, 'index'])
                ->name('onlineorder')->middleware('action.permission:Teacher,read');

            Route::get('posorder', [StudentController::class, 'index'])
                ->name('posorder')->middleware('action.permission:Teacher,read');


            // Customer-related routes
            Route::get('customers/import', [CustomerController::class, 'showImportForm'])->name('customers.import.show');
            Route::post('customers/import', [CustomerController::class, 'import'])->name('customers.import.store');

            Route::get('customers/import/sample-download', function () {
                return response()->download(public_path('samples/customers_import_sample.xlsx'));
            })->name('customers.import.sample');

            Route::post('/customers/ajax-store', [CustomerController::class, 'ajaxStore'])->name('customers.ajaxStore');

            Route::resource('customers', CustomerController::class);

            // Supplier-related routes
            Route::get('suppliers/import', [SupplierController::class, 'showImportForm'])->name('suppliers.import.show');
            Route::post('suppliers/import', [SupplierController::class, 'import'])->name('suppliers.import.store');

            Route::get('suppliers/import/sample-download', function () {
                return response()->download(public_path('samples/suppliers_import_sample.xlsx'));
            })->name('suppliers.import.sample');

            Route::resource('suppliers', SupplierController::class);

            // Category-related routes
            Route::get('categories/import', [CategoryController::class, 'showImportForm'])->name('categories.import.show');
            Route::post('categories/import', [CategoryController::class, 'import'])->name('categories.import.store');

            Route::get('categories/import/sample-download', function () {
                // Define the path to the file in your public directory
                $filePath = public_path('samples/categories_import_sample.csv');

                // Check if the file exists
                if (!file_exists($filePath)) {
                    abort(404, 'The sample file was not found.');
                }

                // Return the file as a download response
                return Response::download($filePath);
            })->name('categories.import.sample');

            Route::resource('categories', CategoryController::class);

            // Tax-related routes

            // ADD THIS ROUTE FOR THE AJAX REQUEST
            Route::post('taxes/ajax-store', [TaxController::class, 'ajaxStore'])->name('taxes.ajaxStore');

            Route::resource('taxes', TaxController::class)->except(['show']);

            // Product-related routes

            Route::get('/products/search', [SaleController::class, 'searchProducts'])->name('products.search');

            Route::get('products/import', [ProductController::class, 'showImportForm'])->name('products.import.show');
            Route::post('products/import', [ProductController::class, 'import'])->name('products.import.store');
            Route::get('products/import/sample-download', function () {
                return response()->download(public_path('samples/products_import_sample.csv'));
            })->name('products.import.sample');

            Route::delete('product-variations/{variation}', [ProductController::class, 'destroyVariation'])->name('product-variations.destroy');

            Route::resource('products', ProductController::class);

            Route::get('/pack-options/{option}/products', [PosController::class, 'getPackOptionProducts'])->name('pos.pack-option.products');

            Route::get('/category-products/{category}/products', [PosController::class, 'getCategoryProducts'])->name('pos.category-product.products');

            Route::resource('packs', PackController::class);

            // Manage Pack related route

            Route::post('manage-packs/options/{option}/products', [ManagePackController::class, 'attachProducts'])->name('manage-packs.options.products.attach');

            Route::delete('manage-packs/options/{option}/products', [ManagePackController::class, 'detachAllProducts'])->name('manage-packs.options.products.detachAll');

            Route::delete('manage-packs/options/{option}/products/{product}', [ManagePackController::class, 'detachProduct'])->name('manage-packs.options.products.detach');

            Route::post('manage-packs/options/{option}/products/reorder', [ManagePackController::class, 'reorderProducts'])->name('manage-packs.options.products.reorder');

            // Manage Pack Group Options Product Variations related route
            Route::get('manage-packs/pack-products/{packProduct}/data', [ManagePackController::class, 'getPackProductData'])->name('manage-packs.pack-products.data');

            // SAVES the selected items/variations for a pack product
            Route::post('manage-packs/pack-products/{packProduct}/items', [ManagePackController::class, 'saveSelectedItems'])->name('manage-packs.pack-products.items.save');

            Route::resource('manage-packs', ManagePackController::class);

            // Route for creating an invoice WITHOUT a payment record
            Route::post('/sales/generate-invoice', [PosController::class, 'storeInvoice'])->name('sales.store.invoice');

            // Route for creating an invoice WITH a payment record
            Route::post('/sales/store-with-payment', [PosController::class, 'storeWithPayment'])->name('sales.store.withPayment');

            // Pos-related routes
            Route::resource('pos', PosController::class);

            // Sales-related routes
            Route::resource('sales', SaleController::class);
        });

        // --- 2. ADMIN ZONE (Protected by the 'admin' middleware) ---
        // Only users with the Super Admin role can access these routes.
        Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {

            // Settings / System Management Routes
            Route::resource('roles', RoleController::class)->except(['show']);
            Route::resource('users', UserController::class)->except(['show']);
            Route::resource('nav-items', NavItemController::class)->except(['show']);

            // Navigation Permission Management
            Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
            Route::get('permissions/{role}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
            Route::put('permissions/{role}', [PermissionController::class, 'update'])->name('permissions.update');

            // Action Permission Management
            Route::get('action-permissions', [ActionPermissionController::class, 'index'])->name('action-permissions.index');
            Route::get('action-permissions/{role}/edit', [ActionPermissionController::class, 'edit'])->name('action-permissions.edit');
            Route::put('action-permissions/{role}', [ActionPermissionController::class, 'update'])->name('action-permissions.update');
        });
    });
});
