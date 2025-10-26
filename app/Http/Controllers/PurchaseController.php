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
use App\Notifications\ModificationRequestApproved;
use PDF;
use Illuminate\Support\Facades\Storage;
use App\Notifications\DocumentReminder;

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

        // Eager-load everything we could possibly need for this complex view
        $purchases = $query->with([
            'suppliers.user',
            'payments',
            'documents.files',
            // Now we get all the nested details for each item
            'items.product.category', // For single products
            'items.variation'
        ])->latest()->paginate(10);

        // Now, process each purchase to calculate the complex data for the view
        $purchases->each(function ($purchase) {
            // 1. Calculate overall purchase financials (already done)
            $purchase->paid_amount = $purchase->payments->sum('amount');
            $purchase->due_amount = $purchase->total_amount - $purchase->paid_amount;
            $purchase->payment_status = $purchase->due_amount <= 0 ? 'Paid' : ($purchase->paid_amount > 0 ? 'Deposit' : 'Unpaid');

            // 2. Calculate overall purchase progress status
            $totalSuppliers = $purchase->suppliers->count();
            $completedSuppliers = $purchase->suppliers->where('pivot.status', 'complete')->count();
            $purchase->progress_text = "{$completedSuppliers} of {$totalSuppliers} Done";

            // 3. Calculate and attach per-supplier data
            foreach ($purchase->suppliers as $supplier) {
                // Get all items for this specific supplier on this purchase
                $supplierItems = $purchase->items->where('supplier_id', $supplier->id);

                // Calculate total price and quantity for this supplier
                $supplier->total_price = $supplierItems->sum('total_price');
                $supplier->total_quantity = $supplierItems->sum('quantity');

                // NOTE: Per-supplier payment tracking is complex. 
                // The design shows it, but our DB doesn't support it yet (payments are linked to the Purchase, not the supplier).
                // For now, we will display 0 for paid and the total as due.
                $supplierPayments = $purchase->payments->where('supplier_id', $supplier->id);

                // Calculate the paid and due amounts for this supplier
                $supplier->paid_amount = $supplierPayments->sum('amount');
                $supplier->due_amount = $supplier->total_price - $supplier->paid_amount;

                // 4. Prepare the document status list for THIS SPECIFIC supplier
                $fileStatus = [];
                // Get all documents for this supplier
                $supplierDocuments = $purchase->documents->where('supplier_id', $supplier->id);

                foreach ($supplierDocuments as $document) {
                    $hasFile = $document->files->isNotEmpty();

                    // --- THIS IS THE CORRECTED LOGIC ---
                    // Condition to display the document in the summary list:
                    // It is either required, OR it is optional but has a file uploaded.
                    if ($document->is_required || $hasFile) {
                        $fileStatus[] = [
                            'name' => pathinfo($document->document_name, PATHINFO_FILENAME),
                            'status' => $hasFile ? 'Ok' : 'Missing', // Status is 'Ok' if a file exists
                        ];
                    }
                }
                $supplier->file_status_list = $fileStatus;
            }

            $suppliersForJs = $purchase->suppliers->map(function ($supplier) {
                return [
                    'id' => $supplier->id,
                    'name' => $supplier->user->name,
                    'due_amount' => $supplier->due_amount, // We already calculated this in the loop above
                ];
            });
            $purchase->suppliers_json = $suppliersForJs->toJson();
        });


        // 4. Get data for the filter dropdowns
        $suppliers = Supplier::with('user')->get()->sortBy('user.name');
        $statuses = [ // Define all possible statuses for your system
            'pending' => 'Pending',
            'ordered' => 'Ordered',
            'partial payment' => 'Deposit Payment',
            'received' => 'Received',
        ];

        // echo '<pre>';
        // print_r($purchases->toArray());
        // echo '</pre>';
        // 5. Pass data to the view
        return view('purchases.index', compact('purchases', 'suppliers', 'statuses'));
    }

    public function storeFromSale(Request $request, Sale $sale)
    {
        // dd($request->all());
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
                    'purchase_number' => 'PO-' . substr($sale->invoice_number, strrpos($sale->invoice_number, '-') + 1),
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

                foreach ($request->suppliers as $supplierData) {
                    $supplierId = $supplierData['supplier_id'];

                    // For each supplier, loop through all possible documents
                    foreach ($possibleDocuments as $docName) {
                        $purchase->documents()->create([
                            'supplier_id' => $supplierId, // <-- THE CRITICAL CHANGE
                            'document_name' => $docName,
                            'is_required' => in_array($docName, $requiredDocuments),
                            'status' => 'pending',
                        ]);
                    }
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
                            'variation_id' => $product['variation_id'] ?? null,
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

                    $itemsForSupplier = $purchase->items()->where('supplier_id', $supplier->id)->get();
                    $supplierTotalAmount = $itemsForSupplier->sum('total_price');
                    // Fire the event for this specific supplier
                    $supplier->user->notify(new PurchaseOrderCreated($purchase, $sender, $itemsForSupplier, $supplierTotalAmount));
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
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\View\View
     */
    public function show(Purchase $purchase)
    {
        // 1. Eager-load all the relationships we need for the detailed view.
        // This is highly efficient and prevents N+1 query problems.
        $purchase->load([
            'sale', // The original sale, if it exists
            'suppliers.user', // All suppliers and their user profiles
            'items.product.category', // All items and their product/category details
            'items.variation', // The specific variation, if applicable
            'documents', // All tracked documents for this purchase
            'payments.supplier.user' // All payments made for this purchase
        ]);

        // 2. Calculate the payment summary.
        $purchase->paid_amount = $purchase->payments->sum('amount');
        $purchase->due_amount = $purchase->total_amount - $purchase->paid_amount;
        if ($purchase->paid_amount <= 0) {
            $purchase->payment_status_text = 'Unpaid';
        } elseif ($purchase->due_amount <= 0) {
            $purchase->payment_status_text = 'Paid';
        } else {
            $purchase->payment_status_text = 'Partial';
        }

        // 3. Group the purchase items by their supplier for easy display in the view.
        $itemsBySupplier = $purchase->items->groupBy('supplier_id');

        // 4. Pass the fully-loaded purchase object and the grouped items to the view.
        return view('purchases.show', compact('purchase', 'itemsBySupplier'));
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
     * A private helper method to prepare data for PDF/Print.
     */
    private function preparePurchaseDataForPdf(Purchase $purchase)
    {
        $purchase->load([
            'sale',
            'suppliers.user',
            'items.product.category',
            'items.variation',
            'documents',
            'payments.supplier.user'
        ]);
        $itemsBySupplier = $purchase->items->groupBy('supplier_id');

        // Calculate payment summary and add it to the purchase object
        $purchase->paid_amount = $purchase->payments->sum('amount');
        $purchase->due_amount = $purchase->total_amount - $purchase->paid_amount;

        return compact('purchase', 'itemsBySupplier');
    }

    /**
     * Display a dedicated view for a specific supplier's items within a purchase order.
     *
     * @param \App\Models\Purchase $purchase
     * @param \App\Models\Supplier $supplier
     * @return \Illuminate\View\View
     */
    public function showSupplierDetails(Purchase $purchase, Supplier $supplier)
    {
        // Security: Double-check that this supplier is actually part of this purchase
        if (!$purchase->suppliers->contains($supplier)) {
            abort(404, 'Supplier not found for this purchase order.');
        }

        // Eager-load the necessary data
        $purchase->load(['sale']);
        $supplier->load(['user']);

        // Get only the items for this specific supplier
        $items = $purchase->items()
            ->where('supplier_id', $supplier->id)
            ->with(['product.category', 'variation'])
            ->get();

        // Calculate this supplier's sub-total
        $supplierSubTotal = $items->sum('total_price');

        // Get the specific pivot data for this supplier
        $pivotData = $purchase->suppliers->find($supplier->id)->pivot;

        return view('purchases.supplier-product-details', compact(
            'purchase',
            'supplier',
            'items',
            'supplierSubTotal',
            'pivotData'
        ));
    }

    public function validateModification(Request $request, Purchase $purchase, Supplier $supplier)
    {

        try {
            // 1. Update the pivot table status for this specific supplier
            $purchase->suppliers()->updateExistingPivot($supplier->id, [
                'status_review' => 'validated', // Or 'validated'
                'status_production' => 'in process',
            ]);

            // 2. Send notification to the supplier
            $adminUser = auth()->user();
            $supplier->user->notify(new ModificationRequestApproved($purchase, $adminUser));
        } catch (\Exception $e) {
            \Log::error('Failed to validate modification: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while validating the modification.');
        }

        return back()->with('success', 'Modification for ' . $supplier->user->name . ' has been validated and they have been notified.');
    }

    /**
     * Generate and stream a PDF for the purchase order.
     */
    public function downloadPdf(Purchase $purchase)
    {
        $data = $this->preparePurchaseDataForPdf($purchase);
        $pdf = PDF::loadView('purchases.pdf', $data);
        $filename = 'PO-' . $purchase->purchase_number . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Show a printable version of the purchase order.
     */
    public function print(Purchase $purchase)
    {
        $data = $this->preparePurchaseDataForPdf($purchase);
        // Pass a 'print' variable to the view to trigger the print script
        $data['print'] = true;

        return view('purchases.pdf', $data);
    }

    /**
     * Store a new payment for an existing purchase via AJAX.
     * This includes handling the optional proof file upload.
     */
    // in app/Http-Controllers/PurchaseController.php

    public function addPayment(Request $request, Purchase $purchase)
    {
        // 1. Calculate the current due amount for validation.
        $paidAmount = $purchase->payments()->sum('amount');
        $dueAmount = $purchase->total_amount - $paidAmount;

        // 2. Validate the incoming request.
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string',
            'amount' => ['required', 'numeric', 'gte:0.01', 'max:' . max(0.01, $dueAmount)],
            'note' => 'nullable|string',
            'proof' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->toArray()], 422);
        }

        // 3. Prepare the data for creating the payment record.
        $paymentData = $validator->validated();

        // 4. Handle the file upload if a proof file is present.
        if ($request->hasFile('proof')) {
            $filePath = $request->file('proof')->store('payment_proofs', 'public');
            $paymentData['proof'] = $filePath;
        }

        // 5. Create the new payment record in the database.
        // This now correctly includes the 'supplier_id'.
        $purchase->payments()->create($paymentData);

        // --- CORRECTION ---
        // The main status of the purchase (e.g., 'ordered', 'shipped') should be
        // managed separately and not be tied directly to payment creation here.
        // We will remove the status update logic.

        // 6. Recalculate the financial summary to return to the frontend for a dynamic update.
        $purchase->refresh(); // Ensures we get the latest data, including the new payment.
        $newPaidAmount = $purchase->payments->sum('amount');
        $newDueAmount = $purchase->total_amount - $newPaidAmount;

        // Determine the new payment status text dynamically.
        if (abs($newPaidAmount - $purchase->total_amount) < 0.01) {
            $newPaymentStatus = 'Paid';
        } else if ($newPaidAmount > 0) {
            $newPaymentStatus = 'Deposit';
        } else {
            $newPaymentStatus = 'Unpaid';
        }

        // 7. Return all the data the frontend needs to update the UI without a page reload.
        return response()->json([
            'success' => true,
            'message' => 'Payment added successfully!',
            'purchase_id' => $purchase->id,
            'new_paid_amount' => number_format($newPaidAmount, 2),
            'new_due_amount' => number_format($newDueAmount, 2),
            'new_payment_status' => $newPaymentStatus,
        ]);
    }

    public function sendDocumentReminder(Request $request, Purchase $purchase, Supplier $supplier)
    {
        try {
            $adminUser = auth()->user();
            $supplier->user->notify(new DocumentReminder($purchase, $adminUser));
        } catch (\Exception $e) {
            \Log::error('Failed to send document reminder: ' . $e->getMessage());
            return back()->with('error', 'Could not send the reminder. Please check mail configuration.');
        }

        return back()->with('success', 'A reminder has been sent to ' . $supplier->company_name . '.');
    }

    public function getPayments(Purchase $purchase)
    {
        // Eager-load the payments and their nested supplier->user relationships for efficiency
        $purchase->load('payments.supplier.user');

        // Calculate the summary
        $totalAmount = $purchase->total_amount;
        $paidAmount = $purchase->payments->sum('amount');
        $dueAmount = $totalAmount - $paidAmount;

        // Format the payment data for easy display in the frontend
        $payments = $purchase->payments->map(function ($payment) {
            return [
                'date' => $payment->payment_date->format('d M, Y'),
                'mode' => $payment->payment_mode,
                'note' => $payment->note,
                'amount' => number_format($payment->amount, 2),
                // Safely get the supplier name
                'supplier_name' => $payment->supplier->user->name ?? 'N/A',
            ];
        });

        return response()->json([
            'success' => true,
            'po_number' => $purchase->purchase_number,
            'total_amount' => number_format($totalAmount, 2),
            'paid_amount' => number_format($paidAmount, 2),
            'due_amount' => number_format($dueAmount, 2),
            'payments' => $payments
        ]);
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
