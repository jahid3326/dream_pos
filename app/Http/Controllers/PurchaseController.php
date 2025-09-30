<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\NewPurchaseOrderNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PurchaseOrderCreated;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Start the query builder
        $query = Purchase::query();

        // 2. Apply filters based on request input
        if ($request->filled('search')) {
            $query->where('purchase_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('supplier_id')) {
            // Filter based on the suppliers relationship
            $query->whereHas('suppliers', function ($q) use ($request) {
                $q->where('suppliers.id', $request->supplier_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Eager-load relationships and paginate the filtered results
        $purchases = $query->with('suppliers.user', 'items', 'payments')->latest()->paginate(10);

        // Add calculated properties for the view
        $purchases->each(function ($purchase) {
            $purchase->paid_amount = $purchase->payments->sum('amount');
            $purchase->due_amount = $purchase->total_amount - $purchase->paid_amount;
            $purchase->payment_status = $purchase->due_amount <= 0 ? 'Paid' : ($purchase->paid_amount > 0 ? 'Partial' : 'Unpaid');
        });

        // 4. Get data for the filter dropdowns
        $suppliers = Supplier::with('user')->get()->sortBy('user.name');
        $statuses = [ // Define all possible statuses for your system
            'pending' => 'Pending',
            'ordered' => 'Ordered',
            'partial payment' => 'Partial Payment',
            'received' => 'Received',
        ];

        // 5. Pass data to the view
        return view('purchases.index', compact('purchases', 'suppliers', 'statuses'));
    }

    public function storeFromSale(Request $request, Sale $sale)
    {
        // 1. Validate the incoming data from the conversion modal
        $validator = Validator::make($request->all(), [
            'suppliers' => 'required|array|min:1',
            'suppliers.*.supplier_id' => 'required|exists:suppliers,id',
            'suppliers.*.products' => 'required|array|min:1',
            'documents' => 'nullable|array',
            'documents.*' => 'string', // Ensure document names are strings
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // 2. Perform all database operations within a transaction.
            // If anything fails, the entire operation is rolled back.
            $purchase = DB::transaction(function () use ($request, $sale) {

                // 2a. Calculate the GRAND TOTAL for the single purchase record.
                $grandTotalAmount = 0;
                foreach ($request->suppliers as $supplierData) {
                    $grandTotalAmount += collect($supplierData['products'])->sum('total_price');
                }

                // 2b. Create ONE central Purchase record linked to the Sale.
                $purchase = Purchase::create([
                    'purchase_number' => $this->generateNextPurchaseNumber(),
                    'purchase_date'   => now(),
                    'status'          => 'ordered',
                    'total_amount'    => $grandTotalAmount,
                    'sale_id'         => $sale->id,
                ]);

                // 2c. Handle the required documents.
                $possibleDocuments = [
                    'Proforma Invoice (PI)',
                    'Packing List',
                    'Certificate of Origin (COO)',
                    'MSDS / Safety Data',
                    'Insurance',
                    'Fumigation Certificate'
                ];
                $requiredDocuments = $request->input('documents', []);

                foreach ($possibleDocuments as $docName) {
                    $purchase->documents()->create([
                        'document_name' => $docName,
                        'is_required' => in_array($docName, $requiredDocuments),
                        'status' => 'pending',
                    ]);
                }

                // 2d. Loop through suppliers from the request to attach them and their items.
                foreach ($request->suppliers as $supplierData) {
                    $supplierId = $supplierData['supplier_id'];

                    // Attach the supplier to the purchase using the pivot table.
                    $purchase->suppliers()->attach($supplierId);

                    // Create the PurchaseItem records for this specific supplier.
                    foreach ($supplierData['products'] as $product) {
                        $purchase->items()->create([
                            'supplier_id'  => $supplierId,
                            'product_id'   => $product['product_id'],
                            'product_name' => $product['product_name'],
                            'quantity'     => $product['quantity'],
                            'unit_price'   => $product['unit_price'],
                            'total_price'  => $product['total_price'],
                        ]);
                    }
                }

                // 2e. Update the original sale's status.
                $sale->update(['order_status' => 'purchase ordered']);

                // Return the created purchase object for use outside the transaction.
                return $purchase;
            });

            $sender = Auth::user();

            // 3. If the transaction was successful, broadcast notifications.
            // This is done outside the transaction to ensure we only notify on success.
            foreach ($request->suppliers as $supplierData) {
                try {
                    $supplier = Supplier::with('user')->findOrFail($supplierData['supplier_id']);

                    // ADD THIS LOG
                    Log::info("[BROADCAST-DEBUG] Firing NewPurchaseOrderNotification for Supplier ID: {$supplier->id}");

                    // Fire the event for this specific supplier
                    $supplier->user->notify(new PurchaseOrderCreated($purchase, $sender));
                } catch (\Exception $e) {
                    // If a single notification fails, log it but don't crash.
                    Log::error("Failed to broadcast PO notification for supplier ID {$supplierData['supplier_id']}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            // If the DB transaction fails, log the detailed error and redirect back.
            Log::error('Sale to Purchase Conversion Failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred. The purchase order was not created.');
        }

        // 4. Redirect to the purchase list with a success message.
        return redirect()->route('purchases.index')->with('success', 'Purchase Order created and notifications sent to suppliers.');
    }

    private function generateNextPurchaseNumber(): string
    {
        $latest = Purchase::latest('id')->first();
        $nextNumber = $latest ? ((int) substr($latest->purchase_number, 3)) + 1 : 1;
        return 'PO-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        // It's good practice to wrap database operations in a try-catch block.
        try {
            // Thanks to `onDelete('cascade')` in your migrations,
            // deleting the purchase will automatically delete all related
            // items, documents, payments, and supplier pivot entries.
            $purchase->delete();
        } catch (\Exception $e) {
            // Log the error for debugging purposes.
            \Log::error('Failed to delete purchase order: ' . $e->getMessage());

            // Redirect back with an error message.
            return redirect()->route('purchases.index')->with('error', 'Failed to delete the purchase order. Please try again.');
        }

        // Redirect back to the list with a success message.
        return redirect()->route('purchases.index')->with('success', 'Purchase Order #' . $purchase->purchase_number . ' has been deleted successfully.');
    }
}
