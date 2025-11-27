<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard based on the user's role.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 1. Get the currently authenticated user
        $user = Auth::user();

        // 2. Check the user's role and redirect to appropriate dashboard
        if ($user->role && $user->role->name === 'Supplier') {
            return $this->supplierDashboard();
        } elseif ($user->role && $user->role->name === 'Shipment') {
            return $this->shipmentDashboard();
        }

        // Default to the Admin dashboard for any other role (Super Admin, Admin, etc.)
        return $this->adminDashboard();
    }

    /**
     * Prepare data and return the view for the Admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    private function adminDashboard()
    {
        // For the admin, you can fetch summary data like total sales, new customers, etc.
        $totalSales = \App\Models\Sale::count();
        $totalCustomers = \App\Models\Customer::count();

        // Determine which tab to show (defaults to 'sales')
        $tab = request('tab', 'sales');

        $sales = null;
        $purchases = null;
        $quotes = null;
        $waiting = null;

        if ($tab === 'sales') {
            $sales = \App\Models\Sale::with(['customer', 'payments'])->latest()->paginate(10);
        } elseif ($tab === 'purchase') {
            $purchases = \App\Models\Purchase::with(['suppliers.user', 'payments'])->latest()->paginate(10);
        } elseif ($tab === 'quotation') {
            $quotes = \App\Models\Quote::with(['customer', 'payments'])->latest()->paginate(10);
        } elseif ($tab === 'waiting') {
            // Purchases where any supplier has status_review not equal to 'complet'
            $waiting = \App\Models\Purchase::whereHas('suppliers', function ($q) {
                $q->where('purchase_supplier.status_review', '!=', 'complet');
            })->with(['suppliers.user', 'payments'])->latest()->paginate(10);
        }

        // Get recent notifications for admin
        $notifications = Auth::user()->unreadNotifications()->latest()->take(5)->get();

        // The admin uses the main 'dashboard.blade.php' view
        // Summary totals
        $purchasesTotal = \App\Models\Purchase::sum('total_amount');
        $salesTotal = \App\Models\Sale::sum('grand_total');
        // Client payments = sum of sale payments
        $clientPaymentsTotal = \App\Models\SalePayment::sum('amount');

        return view('dashboard', [
            'totalSales' => $totalSales,
            'totalCustomers' => $totalCustomers,
            'notifications' => $notifications,
            'tab' => $tab,
            'sales' => $sales,
            'purchases' => $purchases,
            'quotes' => $quotes,
            'waiting' => $waiting,
            'purchasesTotal' => $purchasesTotal,
            'salesTotal' => $salesTotal,
            'clientPaymentsTotal' => $clientPaymentsTotal,
        ]);
    }

    /**
     * Prepare data and return the view for the Shipment dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function shipmentDashboard()
    {
        // Get pending shipments
        $pendingShipments = \App\Models\Shipment::whereHas('purchase', function ($query) {
            $query->where('status', '!=', 'completed');
        })->with(['customer.user', 'purchase'])->latest()->take(10)->get();

        // Get recent shipments
        $recentShipments = \App\Models\Shipment::with(['customer.user', 'purchase'])
            ->latest()->take(5)->get();

        // Get notifications for shipment user
        $notifications = Auth::user()->unreadNotifications()->latest()->take(10)->get();

        // Statistics
        $totalShipments = \App\Models\Shipment::count();
        $pendingCount = \App\Models\Shipment::whereHas('purchase', function ($query) {
            $query->where('status', '!=', 'completed');
        })->count();

        return view('dashboard.shipment', [
            'pendingShipments' => $pendingShipments,
            'recentShipments' => $recentShipments,
            'notifications' => $notifications,
            'totalShipments' => $totalShipments,
            'pendingCount' => $pendingCount,
        ]);
    }

    /**
     * Prepare data and return the view for the Supplier dashboard.
     *
     * @return \Illuminate\View\View
     */
    private function supplierDashboard()
    {
        // 1. Get the user's supplier profile.
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'Supplier profile not found.');
        }

        // 2. Fetch all purchases this supplier is a part of.
        // The relationship automatically loads pivot data because we configured it in the Purchase model.
        $activities = $supplier->purchases()
            ->with(['sale', 'payments', 'documents.files', 'items'])
            ->latest('id')
            ->paginate(10);

        // 3. Process each activity to calculate display-specific data.
        $activities->each(function ($activity) use ($supplier) {

            // a. Calculate the total amount for ONLY this supplier's items.
            $supplierItems = $activity->items->where('supplier_id', $supplier->id);
            $activity->supplier_total_amount = $supplierItems->sum('total_price');

            // b. Get the per-supplier statuses directly from the pivot table data.
            // The 'pivot' attribute is automatically available on models from a many-to-many relationship.
            $activity->status_review = $activity->pivot->status_review;
            $activity->status_production = $activity->pivot->status_production;

            // c. Calculate the payment status for the OVERALL Purchase Order.
            $paidAmount = $activity->payments->sum('amount');
            if ($paidAmount <= 0) {
                $activity->payment_status_text = 'Waiting Payment';
            } elseif ($paidAmount >= $activity->total_amount) {
                $activity->payment_status_text = 'Full Payed';
            } else {
                $activity->payment_status_text = 'Deposit Payed';
            }

            // d. Prepare the document/files list.
            $files = [];
            $hasMissingFiles = false;
            // Get all documents for this purchase belonging to this supplier
            $supplierDocuments = $activity->documents->where('supplier_id', '==', $supplier->id);

            // First, check for the "missing" flag among required documents
            $hasMissingFiles = $supplierDocuments
                ->where('is_required', true)
                ->first(fn($doc) => $doc->files->isEmpty()) !== null;

            // Now, build the display list for the files column.
            foreach ($supplierDocuments as $document) {
                $hasFile = $document->files->isNotEmpty();

                // --- THIS IS THE CORRECTED LOGIC ---
                // Condition to display: It's required, OR it's optional but has a file.
                if ($document->is_required || $hasFile) {
                    $files[] = [
                        'name' => pathinfo($document->document_name, PATHINFO_FILENAME),
                        'status' => $hasFile ? 'Ok' : 'Missing',
                    ];
                }
            }
            $activity->file_list = $files;
            $activity->has_missing_files = $hasMissingFiles;
        });

        // echo '<pre>';
        // print_r($activities->toArray());
        // echo '<pre>';
        // exit;
        // 4. Compute summary counts for production statuses (for this supplier)
        $newOrdersCount = $supplier->purchases()->wherePivot('status_production', 'waiting')->count();
        $inProcessCount = $supplier->purchases()->wherePivot('status_production', 'in process')->count();

        // Some records may use slight variations (e.g. 'complet' or 'completed') - check both
        $completeCount = \Illuminate\Support\Facades\DB::table('purchase_supplier')
            ->where('supplier_id', $supplier->id)
            ->whereIn('status_production', ['complet', 'completed'])
            ->count();

        return view('supplier.dashboard', compact('activities', 'newOrdersCount', 'inProcessCount', 'completeCount'));
    }
}
