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
    public function show($orderId)
    {
        $order = Purchase::find($orderId);
        // print_r($order->toArray());

        $user = Auth::user();
        $supplier = $user->supplierProfile;

        // Security Check: Ensure the purchase belongs to this supplier
        if (!$order->suppliers->contains($supplier)) {
            abort(403, 'You do not have permission to view this order.');
        }

        // Eager-load all necessary data
        $order->load(['sale', 'documents', 'payments', 'items.product.category', 'items.variation']);

        // --- Data Preparation for the View ---

        // 1. Get only the items for THIS supplier
        $items = $order->items->where('supplier_id', $supplier->id);

        // 2. Calculate this supplier's specific financials
        $supplierTotal = $items->sum('total_price');
        // For now, payments are global to the PO. We'll show the overall payment status.
        $paidAmount = $order->payments->sum('amount');
        $dueAmount = $order->total_amount - $paidAmount; // Based on the whole PO

        // Determine payment status text
        if ($paidAmount <= 0) {
            $paymentStatus = 'Deposit'; // Or 'Unpaid'
        } elseif ($dueAmount <= 0) {
            $paymentStatus = 'Full Payed';
        } else {
            $paymentStatus = 'Deposit';
        }

        // 3. Prepare document status list and check if information is missing
        // Prepare document status list
        $file_list = [];
        $hasMissingInfo = false;
        // Filter documents for this purchase AND this supplier
        $supplierDocuments = $order->documents->where('supplier_id', $supplier->id);

        foreach ($supplierDocuments as $document) {
            // A file is missing IF it is required AND has no file_path.
            if ($document->is_required && !$document->file_path) {
                $hasMissingInfo = true;
            }

            $file_list[] = [
                'id' => $document->id,
                'name' => pathinfo($document->document_name, PATHINFO_FILENAME),
                'is_required' => $document->is_required,
                'file_path' => $document->file_path,
                'status' => $document->status,
            ];
        }

        // 4. Calculate progress bar percentage (example logic)
        // Let's say progress is based on production status.
        $progress = 0;
        if ($order->status_production === 'in process') {
            $progress = 50;
        } elseif ($order->status_production === 'complet') {
            $progress = 100;
        }

        return view('supplier.orders.show', compact(
            'order',
            'items',
            'supplierTotal',
            'paidAmount',
            'dueAmount',
            'paymentStatus',
            'file_list',
            'hasMissingInfo',
            'progress'
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
