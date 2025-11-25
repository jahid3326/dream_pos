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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ManagePackController;
use App\Http\Controllers\OrderActionController;
use App\Http\Controllers\PackController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\Supplier\OrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaxController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

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

// Redirect root to login if not authenticated, or dashboard if authenticated
Route::get('/', function () {
    // This check handles both cases elegantly
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/shipment', [DashboardController::class, 'shipmentDashboard'])->name('dashboard.shipment');

    // Test notification route
    Route::get('/test-notification', function () {
        return view('test-notification');
    })->name('test.notification.form');

    Route::post('/test-notification', function () {
        try {
            // Get all shipment users
            $shipmentUsers = \App\Models\User::whereHas('role', function ($query) {
                $query->where('name', 'Shipment');
            })->get();

            if ($shipmentUsers->isEmpty()) {
                return back()->with('error', 'No Shipment users found in the system.');
            }

            // Create a test shipment for notification
            $testShipment = new \App\Models\Shipment();
            $testShipment->purchase_id = 1; // Use any existing purchase ID
            $testShipment->agent_id = auth()->id();
            $testShipment->total_price = 100.00;
            $testShipment->payment_status = 'pending';
            $testShipment->status = 'In Process';
            $testShipment->save();

            // Send notification to all shipment users with current user as sender
            foreach ($shipmentUsers as $user) {
                $user->notify(new \App\Notifications\NewShipmentNotification($testShipment, auth()->user()));
            }

            return back()->with('success', 'Test notification sent to ' . $shipmentUsers->count() . ' Shipment users.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error sending notification: ' . $e->getMessage());
        }
    })->name('test.notification');
    // Logout route for all authenticated users
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Super Admin Routes
    Route::middleware(['auth'])->group(function () {

        // Add a check here to ensure only Super Admin can access these routes
        // This is a simple inline middleware for demonstration

        // --- 1. GENERAL APPLICATION ROUTES (Permission controlled by 'nav.permission') ---
        // These routes are accessible to any role that has been granted permission by the Super Admin.

        Route::middleware(['nav.permission'])->group(function () {
            // Customer-related routes
            Route::get('customers/import', [CustomerController::class, 'showImportForm'])->name('customers.import.show');
            Route::post('customers/import', [CustomerController::class, 'import'])->name('customers.import.store');

            Route::get('customers/import/sample-download', function () {
                return response()->download(public_path('samples/customers_import_sample.xlsx'));
            })->name('customers.import.sample');

            Route::post('/customers/ajax-store', [CustomerController::class, 'ajaxStore'])->name('customers.ajaxStore');

            Route::get('/customers/{customer}/unpaid-invoices', [PaymentController::class, 'getUnpaidInvoices'])->name('customers.unpaid-invoices');

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

            Route::get('/pack-options/search', [SaleController::class, 'searchPackOptions'])->name('sales.pack-options.search');

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

            Route::post('/sales/{sale}/payments', [SaleController::class, 'addPayment'])->name('sales.payments.store');
            Route::get('/sales/{sale}/invoice-pdf', [SaleController::class, 'viewInvoicePdf'])->name('sales.view.pdf');
            Route::get('/sales/{sale}/download-invoice-pdf', [SaleController::class, 'downloadInvoicePdf'])->name('sales.downloadInvoice.pdf');
            Route::get('/sales/{sale}/print-invoice', [SaleController::class, 'printInvoice'])->name('sales.print.invoice');
            Route::get('/sales/{sale}/payments', [SaleController::class, 'getPayments'])->name('sales.payments.get');

            // For fetching data for the modal
            Route::get('/sales/{sale}/purchase-preview', [SaleController::class, 'getPurchasePreview'])->name('sales.purchasePreview');

            // For handling the conversion submission
            Route::post('/sales/{sale}/convert-to-purchase', [PurchaseController::class, 'storeFromSale'])->name('sales.convertToPurchase');


            // Sales-related routes
            // Route to mark a sale as complete
            Route::post('/sales/{sale}/complete', [SaleController::class, 'markComplete'])->name('sales.markComplete');

            Route::resource('sales', SaleController::class);

            // Route for creating a quote from the POS
            Route::post('/quotes/generate-quote', [QuoteController::class, 'store'])->name('quotes.store');
            Route::get('/quotes/{quote}/quote-pdf', [QuoteController::class, 'viewQuotePdf'])->name('quotes.view.pdf');
            Route::post('/quotes/{quote}/convert-to-sale', [QuoteController::class, 'convertToSale'])->name('quotes.convertToSale');

            // You can add a full resource route for managing quotes later
            Route::resource('quotes', QuoteController::class);

            // Payments-related routes
            Route::resource('payments', PaymentController::class)->except(['show']);

            // Route to fetch all payments for a specific purchase (for the modal)
            Route::get('/purchases/{purchase}/payments', [PurchaseController::class, 'getPayments'])
                ->name('purchases.payments.get');

            // Route to store a new payment for a specific purchase
            Route::post('/purchases/{purchase}/payments', [PurchaseController::class, 'addPayment'])
                ->name('purchases.payments.store');

            Route::get('/purchases/{purchase}/download-pdf', [PurchaseController::class, 'downloadPdf'])
                ->name('purchases.downloadPdf');

            Route::get('/purchases/{purchase}/print', [PurchaseController::class, 'print'])
                ->name('purchases.print');

            Route::get('/purchases/{purchase}/supplier/{supplier}', [App\Http\Controllers\PurchaseController::class, 'showSupplierDetails'])
                ->name('purchases.showSupplierDetails');

            Route::post('/purchases/{purchase}/supplier/{supplier}/validate-modification', [PurchaseController::class, 'validateModification'])
                ->name('purchases.validateModification');

            // Full resource routes for managing purchases
            Route::resource('purchases', PurchaseController::class);

            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

            Route::post('/purchases/{purchase}/supplier/{supplier}/send-document-reminder', [PurchaseController::class, 'sendDocumentReminder'])
                ->name('purchases.sendDocumentReminder');

            // Shipment-related routes
            Route::post('/purchases/{purchase}/convert-to-shipping', [ShipmentController::class, 'storeFromPurchase'])->name('purchases.convertToShipping');

            Route::get('/shipments/{shipment}/get-payments', [ShipmentController::class, 'getPayments'])
                ->name('shipments.getPayments');

            Route::post('/shipments/{shipment}/payments', [ShipmentController::class, 'addPayment'])->name('shipments.addPayment');

            // --- ADD THESE ROUTES FOR TRACKING ---
            Route::post('/shipments/{shipment}/tracking', [ShipmentController::class, 'addTracking'])->name('shipments.addTracking');
            Route::delete('/shipments/{shipment}/tracking', [ShipmentController::class, 'removeTracking'])->name('shipments.removeTracking');

            // --- ORDER DETAIL DOCUMENT ROUTES ---
            Route::get('/shipments/{shipment}/order-detail-document', [ShipmentController::class, 'orderDetailDocument'])->name('shipments.orderDetailDocument');
            Route::post('/shipments/{shipment}/documents', [ShipmentController::class, 'uploadDocument'])->name('shipments.uploadDocument');
            Route::delete('/shipments/{shipment}/documents/{document}', [ShipmentController::class, 'deleteDocument'])->name('shipments.deleteDocument');

            // Resource routes for managing shipments
            Route::resource('shipments', ShipmentController::class);
        });

        Route::get('/orders/{purchase}/details', [OrderController::class, 'details'])->name('orders.details');
        // We define the 'show' route manually to customize the binding.
        Route::get('orders/{purchase}', [OrderController::class, 'show'])->name('orders.show');
        // Routes for the actions (confirm, reject, etc.) remain the same
        Route::post('/orders/{purchase}/confirm', [OrderActionController::class, 'confirm'])->name('orders.confirm');
        Route::get('/orders/{purchase}/propose-modification', [OrderActionController::class, 'showModificationForm'])->name('orders.showModificationForm');

        // The POST route for submitting the proposal already exists, which is great.
        Route::post('/orders/{purchase}/propose-modification', [OrderActionController::class, 'proposeModification'])->name('orders.proposeModification');

        Route::get('/orders/{purchase}/documents', [DocumentController::class, 'showUploadForm'])->name('documents.showUploadForm');

        Route::post('/orders/{purchase}/documents/save', [DocumentController::class, 'saveDocuments'])->name('documents.save');

        // --- CHANGE THIS ROUTE ---
        // Instead of deleting a document, we delete a specific file from a document.
        Route::delete('/document-files/{file}', [DocumentController::class, 'destroyFile'])->name('document-files.destroy');

        // You can keep the 'index' route from the resource if you want.
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

        Route::post('/documents/{document}/upload', [DocumentController::class, 'upload'])->name('documents.upload');

        // Notification routes (outside nav.permission middleware to avoid 403 errors)
        Route::post('/notifications/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAsRead');
        Route::post('/notifications/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsReadSingle');
        Route::get('/notifications/count', [App\Http\Controllers\NotificationController::class, 'getCount'])->name('notifications.getCount');
        Route::get('/notifications/latest', [App\Http\Controllers\NotificationController::class, 'getLatest'])->name('notifications.getLatest');

        // Global search routes
        Route::get('/global-search', [App\Http\Controllers\SearchController::class, 'index'])->name('global.search');
        Route::get('/global-search/suggest', [App\Http\Controllers\SearchController::class, 'suggest'])->name('global.search.suggest');

        // Test route to debug middleware issues
        Route::get('/test-auth', function () {
            return response()->json([
                'authenticated' => Auth::check(),
                'user' => Auth::user() ? Auth::user()->name : null,
                'role' => Auth::user() && Auth::user()->role ? Auth::user()->role->name : null
            ]);
        })->name('test.auth');

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

// Test route for notifications (remove in production)
Route::get('/test-notification/{userId}', function ($userId) {
    $user = App\Models\User::find($userId);

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Get the latest shipment for testing
    $shipment = App\Models\Shipment::latest()->first();

    if (!$shipment) {
        return response()->json(['error' => 'No shipment found for testing'], 404);
    }

    // Send test notification with current authenticated user as sender
    $user->notify(new App\Notifications\NewShipmentNotification($shipment, auth()->user()));

    return response()->json([
        'success' => true,
        'message' => 'Test notification sent to user: ' . $user->name,
        'shipment' => $shipment->shipment_number
    ]);
})->middleware('auth')->name('test.notification');
