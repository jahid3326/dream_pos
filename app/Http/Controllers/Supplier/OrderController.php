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

        // --- SECURITY CHECK ---
        // Ensure the requested order actually belongs to this supplier.
        if (!$order->suppliers->contains($supplier)) {
            abort(403, 'You do not have permission to view this order.');
        }

        // Eager-load all necessary relationships
        $order->load(['sale', 'documents', 'payments']);

        // Get only the items for this specific supplier
        $items = $order->items()
            ->where('supplier_id', $supplier->id)
            ->with(['product.category', 'variation'])
            ->get();

        // Calculate this supplier's portion of the financials
        $supplierTotal = $items->sum('total_price');
        // Note: Per-supplier payment tracking is complex. This is a simplified view.
        $paidAmount = 0; // Placeholder for now
        $dueAmount = $supplierTotal - $paidAmount;

        // Prepare document status list
        $file_list = [];
        foreach ($order->documents->where('is_required', true) as $document) {
            $isOk = $document->status === 'approved';
            $file_list[] = [
                'name' => pathinfo($document->document_name, PATHINFO_FILENAME),
                'status' => $isOk ? 'Ok' : 'Missing',
            ];
        }

        return view('supplier.orders.show', compact('order', 'items', 'supplierTotal', 'paidAmount', 'dueAmount', 'file_list'));
    }
}
