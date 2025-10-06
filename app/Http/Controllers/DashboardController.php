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

        // 2. Check the user's role. We'll check for 'Supplier' first,
        //    and assume others are 'Admin' or similar for this example.
        //    You can add more roles like 'Customer' here as needed.
        if ($user->role && $user->role->name === 'Supplier') {
            return $this->supplierDashboard();
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
        // This is just an example.
        $totalSales = \App\Models\Sale::count();
        $totalCustomers = \App\Models\Customer::count();

        // The admin uses the main 'dashboard.blade.php' view
        return view('dashboard', [
            'totalSales' => $totalSales,
            'totalCustomers' => $totalCustomers,
        ]);
    }

    /**
     * Prepare data and return the view for the Supplier dashboard.
     *
     * @return \Illuminate\View\View
     */
    private function supplierDashboard()
    {
        // 1. Get the user's associated supplier profile
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            // This is a safety check in case a user has the 'Supplier' role
            // but no supplier profile is linked.
            abort(403, 'Supplier profile not found.');
        }

        // 2. Fetch the "Recent Activity" data for this specific supplier
        $activities = $supplier->purchases()
            ->with(['sale', 'payments', 'documents'])
            ->latest('purchase_date')
            ->paginate(10);

        // 3. Process each activity to calculate display-specific data
        $activities->each(function ($activity) {
            $paidAmount = $activity->payments->sum('amount');
            if ($paidAmount <= 0) {
                $activity->payment_status_text = 'Waiting Payment';
            } elseif ($paidAmount >= $activity->total_amount) {
                $activity->payment_status_text = 'Full Payed';
            } else {
                $activity->payment_status_text = 'Deposit Payed';
            }

            $files = [];
            $hasMissingFiles = false;
            foreach ($activity->documents->where('is_required', true) as $document) {
                $isOk = $document->status === 'approved'; // Example logic
                if (!$isOk) $hasMissingFiles = true;
                $files[] = [
                    'name' => pathinfo($document->document_name, PATHINFO_FILENAME),
                    'status' => $isOk ? 'Ok' : 'Missing',
                ];
            }
            $activity->file_list = $files;
            $activity->has_missing_files = $hasMissingFiles;
        });

        // 4. Return the dedicated supplier dashboard view with the data
        return view('supplier.dashboard', compact('activities'));
    }
}
