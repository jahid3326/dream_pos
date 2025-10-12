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
        // 1. Get the user's supplier profile.
        $supplier = Auth::user()->supplierProfile;

        if (!$supplier) {
            abort(403, 'Supplier profile not found.');
        }

        // 2. Fetch all purchases this supplier is a part of.
        // The relationship automatically loads pivot data because we configured it in the Purchase model.
        $activities = $supplier->purchases()
            ->with(['sale', 'payments', 'documents', 'items'])
            ->latest('purchase_date')
            ->paginate(15);

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
            $supplierDocuments = $activity->documents->where('supplier_id', $supplier->id);

            foreach ($supplierDocuments as $document) {
                $hasFile = !is_null($document->file_path);

                // Check if a required file is missing
                if ($document->is_required && !$hasFile) {
                    $hasMissingFiles = true;
                }

                // Condition to display the document in the summary list:
                // It's required, OR it's optional but has a file.
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
        // 4. Return the view with the processed data.
        return view('supplier.dashboard', compact('activities'));
    }
}
