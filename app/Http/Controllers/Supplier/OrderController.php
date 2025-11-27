<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('action.permission:Purchase,read')->only('index');
        $this->middleware('action.permission:Purchase,create')->only(['create', 'store']);
        $this->middleware('action.permission:Purchase,show')->only('show');
        $this->middleware('action.permission:Purchase,update')->only(['edit', 'update']);
        $this->middleware('action.permission:Purchase,delete')->only('destroy');
    }

    /**
     * Display the specified purchase order, but scoped to the logged-in supplier.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\View\View
     */
    public function show(Purchase $purchase)
    {
        $user = Auth::user();
        $supplier = $user->supplierProfile;

        // --- SECURITY CHECK ---
        if (!$supplier || !$purchase->suppliers->contains($supplier->id)) {
            abort(403, 'You do not have permission to view this order.');
        }

        // --- DATA PREPARATION ---

        // 1. Eager-load all necessary relationships for the view.
        $purchase->load([
            'sale',
            'documents.files',
            'suppliers.user', // Load all suppliers to get the pivot data
            'payments' => function ($query) use ($supplier) {
                // Pre-filter payments to get only those for THIS supplier.
                $query->where('supplier_id', $supplier->id);
            },
            'items' => function ($query) use ($supplier) {
                // Pre-filter items for THIS supplier.
                $query->where('supplier_id', $supplier->id)->with(['product.category', 'variation']);
            }
        ]);

        // 2. The filtered items and payments are now available on the purchase object.
        $items = $purchase->items;
        $supplierPayments = $purchase->payments;

        // 3. Calculate this supplier's specific financials.
        $supplierTotal = $items->sum('total_price');
        $paidAmount = $supplierPayments->sum('amount'); // This is now supplier-specific
        $dueAmount = $supplierTotal - $paidAmount;    // This is now supplier-specific

        // 4. Determine THIS SUPPLIER's payment status text.
        $paymentStatus = 'Unpaid';
        if ($paidAmount > 0) {
            $paymentStatus = ($dueAmount <= 0.01) ? 'Paid' : 'Partial';
        }

        // 5. Get the pivot data for statuses and progress.
        $supplierPivot = $purchase->suppliers->find($supplier->id)->pivot;
        $progress = 0; // ... progress calculation ...

        // 6. Get the list of documents for THIS supplier to display in the tab.
        $file_list = $purchase->documents->where('supplier_id', $supplier->id);

        // 7. Check if there are any required documents in that list that have no files.
        $hasMissingInfo = $file_list
            ->where('is_required', true)
            ->first(function ($document) {
                // This closure will run for each required document.
                // It checks if the 'files' relationship collection is empty.
                return $document->files->isEmpty();
            }) !== null; // first() returns the first item that matches, or null if none match.

        // For the "Other Suppliers" section (if you need it later)
        $otherSuppliers = $purchase->suppliers->where('id', '!=', $supplier->id);
        $itemsBySupplier = $purchase->items()->get()->groupBy('supplier_id');

        // 9. Pass all the prepared data to the view.
        return view('supplier.orders.show', compact(
            'purchase',
            'items',
            'supplierTotal',
            'paidAmount', // This is now the supplier-specific paid amount
            'dueAmount',  // This is now the supplier-specific due amount
            'paymentStatus',
            'file_list',
            'hasMissingInfo',
            'progress',
            'supplierPivot'
        ));
    }

    /**
     * Display the dedicated order detail and action page for a supplier.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\View\View
     */
    public function details(Purchase $purchase)
    {
        // 1. Get the logged-in user's supplier profile.
        $user = Auth::user();
        $supplier = $user->supplierProfile;

        // 2. --- SECURITY CHECK ---
        // Ensure the requested purchase actually involves this supplier.
        if (!$supplier || !$purchase->suppliers->contains($supplier->id)) {
            abort(403, 'You do not have permission to view this order.');
        }

        // 3. Eager-load all necessary relationships for the view.
        $purchase->load([
            'sale',
            'documents' // Load all documents for the purchase
        ]);

        // 4. Get only the items for THIS specific supplier from the purchase.
        $items = $purchase->items()
            ->where('supplier_id', $supplier->id)
            ->with(['product.category', 'variation']) // Load details for CBM, images, etc.
            ->get();

        // 5. Calculate this supplier's sub-total based on their items.
        $supplierSubTotal = $items->sum('total_price');

        // 6. Check for missing documents for THIS SPECIFIC supplier.
        $hasMissingDocuments = $purchase->documents
            ->where('supplier_id', $supplier->id) // Filter documents for this supplier
            ->where('is_required', true)           // Only check required documents
            ->whereNull('file_path')               // Check if the file has been uploaded
            ->isNotEmpty();                        // Returns true if any are missing

        // 7. Pass all the prepared data to the 'details' view.
        return view('supplier.orders.details', compact(
            'purchase',
            'items',
            'supplierSubTotal',
            'hasMissingDocuments'
        ));
    }
}
