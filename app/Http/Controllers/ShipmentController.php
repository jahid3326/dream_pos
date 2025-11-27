<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Shipment;
use App\Models\ShipmentDocument;
use App\Models\User;
use App\Notifications\NewShipmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Models\PurchaseSupplier;

class ShipmentController extends Controller
{
    public function storeFromPurchase(Request $request, Purchase $purchase)
    {
        if ($purchase->shipment) {
            return redirect()->back()->with('error', 'A shipment already exists for this purchase.');
        }

        try {
            $shipment = null;
            DB::transaction(function () use ($purchase, &$shipment) {
                $customer = $purchase->sale->customer;
                if (!$customer) throw new \Exception("Customer not found.");

                $purchaseNumber = substr($purchase->purchase_number, strrpos($purchase->purchase_number, '-') + 1);
                $shipmentNumber = 'SHIP-' . $purchaseNumber;

                $shipment = Shipment::create([
                    'shipment_number' => $shipmentNumber,
                    'customer_id'     => $customer->id,
                    'purchase_id'     => $purchase->id,
                    'shipment_date'   => now(),
                    'total_amount'    => 0.00, // Shipping cost is initially 0, to be added later
                ]);
            });

            // Send notifications to all users with 'Shipment' role
            if ($shipment) {
                $shipmentUsers = User::whereHas('role', function ($query) {
                    $query->where('name', 'Shipment');
                })->get();

                if ($shipmentUsers->count() > 0) {
                    // Pass the current authenticated user as the sender
                    Notification::send($shipmentUsers, new NewShipmentNotification($shipment, auth()->user()));
                }
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating shipment: ' . $e->getMessage());
        }

        return redirect()->route('shipments.index')->with('success', 'Purchase converted to shipment. Notifications sent to shipment team.');
    }

    public function index()
    {
        $shipments = Shipment::with([
            'customer.user',
            'payments', // Eager-load shipment payments
            'purchase.suppliers.user',
            'purchase.items',
        ])->latest()->paginate(15);

        $shipments->each(function ($shipment) {
            // Calculate payment status for the SHIPPING COST
            $shipment->paid_amount = $shipment->payments->sum('amount');
            $shipment->due_amount = $shipment->total_amount - $shipment->paid_amount;
            $shipment->payment_status = 'Unpaid';
            if ($shipment->paid_amount > 0) {
                $shipment->payment_status = ($shipment->due_amount <= 0.01) ? 'Paid' : 'Partial';
            }
            // Prepare supplier data for the view
            foreach ($shipment->purchase->suppliers as $supplier) {
                $supplierItems = $shipment->purchase->items->where('supplier_id', $supplier->id);
                $supplier->total_quantity = $supplierItems->sum('quantity');
            }
        });

        return view('shipments.index', compact('shipments'));
    }

    // You will also need edit/update methods to set the shipping 'total_amount'

    /**
     * Display the specified resource.
     */
    public function show(Shipment $shipment)
    {
        // 1. Eager-load the primary relationships.
        $shipment->load([
            'customer.user',
            'payments',
            'purchase.sale',
            'purchase.suppliers.user', // This is still good for getting the list of suppliers
            'purchase.documents.files'
        ]);

        // --- Prepare data for the view ---

        // ... (Your existing logic for payment status, tracking, etc. is fine) ...
        $paidAmount = $shipment->payments->sum('amount');
        $dueAmount = $shipment->total_amount - $paidAmount;
        $paymentStatus = 'Unpaid';
        if ($paidAmount > 0) {
            $paymentStatus = ($dueAmount <= 0.01) ? 'Paid' : 'Deposit';
        }
        // ...

        // --- THIS IS THE CORRECTED AND FINAL LOGIC ---
        $totalCbm = 0;
        $totalWeight = 0;

        // 2. Iterate through the suppliers to process each one.
        foreach ($shipment->purchase->suppliers as $supplier) {

            // a. Manually fetch the full Eloquent Pivot Model for this supplier/purchase.
            $pivot = PurchaseSupplier::where('purchase_id', $shipment->purchase->id)
                ->where('supplier_id', $supplier->id)
                ->first();

            // b. If the pivot model exists, load its nested relationships.
            if ($pivot) {
                $pivot->load('cargo.dimensions');
            }

            // c. Assign the fully-loaded pivot object (or null) to the stable property.
            $supplier->purchase_details = $pivot;

            // d. Perform calculations using the now-loaded relationships.
            if ($supplier->purchase_details && $supplier->purchase_details->cargo) {
                $totalCbm += $supplier->purchase_details->cargo->total_cbm ?? 0;
                $totalWeight += $supplier->purchase_details->cargo->gross_weight ?? 0;
            }
        }
        // --- END OF CORRECTION ---

        $trackingUrls = $shipment->tracking_urls ?? [];

        // --- Determine the Shipment Agent ---
        $shipmentAgent = null;

        // Option 1: Try to find a user with 'Shipment Agent' role
        $shipmentAgentRole = \App\Models\Role::where('name', 'like', '%agent%')->orWhere('name', 'like', '%shipment%')->first();
        if ($shipmentAgentRole) {
            $shipmentAgent = $shipmentAgentRole->users()->first();
        }

        // Option 2: If no agent role found, use the person who created the related sale
        if (!$shipmentAgent && $shipment->purchase && $shipment->purchase->sale && $shipment->purchase->sale->created_by) {
            $shipmentAgent = \App\Models\User::find($shipment->purchase->sale->created_by);
        }

        // Option 3: Fallback to a Super Admin
        if (!$shipmentAgent) {
            $superAdminRole = \App\Models\Role::where('name', 'Super Admin')->first();
            if ($superAdminRole) {
                $shipmentAgent = $superAdminRole->users()->first();
            }
        }

        // Option 4: Final fallback to any user if nothing else works
        if (!$shipmentAgent) {
            $shipmentAgent = \App\Models\User::first();
        }

        return view('shipments.show', compact(
            'shipment',
            'paidAmount',
            'dueAmount',
            'paymentStatus',
            'totalCbm',
            'totalWeight',
            'trackingUrls',
            'shipmentAgent'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipment $shipment)
    {
        // This is where an admin can edit shipment details, like the shipping cost.
        return view('shipments.edit', compact('shipment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shipment $shipment)
    {
        // Check if it's an AJAX request (from order-detail-document page)
        if ($request->ajax()) {
            // Authorization: Only shipment role users can update shipment details
            // if (!auth()->user() || !auth()->user()->role || auth()->user()->role->name !== 'Shipment') {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Unauthorized. Only shipment role users can update shipment details.'
            //     ], 403);
            // }

            $request->validate([
                'delivery_estimation_date' => 'nullable|date',
                'total_amount' => 'required|numeric|min:0',
                'shipping_type_id' => 'nullable|exists:shipping_types,id',
                'shipping_tax_id' => 'nullable|exists:shipping_taxes,id',
                'container' => 'nullable|array',
            ]);

            $shipment->update([
                'delivery_estimation_date' => $request->delivery_estimation_date,
                'total_amount' => $request->total_amount,
                'shipping_type_id' => $request->shipping_type_id,
                'shipping_tax_id' => $request->shipping_tax_id,
                'container' => $request->container,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shipment updated successfully.',
            ]);
        }

        // Regular form submission (from edit page)
        $request->validate([
            'total_amount' => 'required|numeric|min:0',
            // Add other updatable fields here
        ]);

        $shipment->update($request->all());

        return redirect()->route('shipments.index')->with('success', 'Shipment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment)
    {
        try {
            // The onDelete('cascade') on purchase_id will fail if the purchase still exists.
            // We should just delete the shipment. The related purchase remains.
            $shipment->purchase()->update(['status' => 'ordered']); // Revert purchase status
            $shipment->delete();
        } catch (\Exception $e) {
            return redirect()->route('shipments.index')->with('error', 'Could not delete shipment.');
        }

        return redirect()->route('shipments.index')->with('success', 'Shipment deleted successfully.');
    }

    /**
     * Fetch all payments for a specific shipment via AJAX.
     */
    public function getPayments(Shipment $shipment)
    {
        $shipment->load('payments');

        $payments = $shipment->payments->map(function ($payment) {
            return [
                'date' => $payment->payment_date->format('d M, Y'),
                'mode' => $payment->payment_mode,
                'note' => $payment->note,
                'amount' => number_format($payment->amount, 2),
            ];
        });

        return response()->json([
            'success' => true,
            'shipment_number' => $shipment->shipment_number,
            'payments' => $payments
        ]);
    }

    // You will need a method to add tracking URLs
    public function addTracking(Request $request, Shipment $shipment)
    {
        $request->validate(['tracking_url' => 'required|url']);

        $urls = $shipment->tracking_urls ?? [];
        $urls[] = $request->tracking_url;

        $shipment->update(['tracking_urls' => $urls]);

        return back()->with('success', 'Tracking URL added.');
    }

    public function removeTracking(Request $request, Shipment $shipment)
    {
        $request->validate(['tracking_url' => 'required|url']);

        // Get the current URLs, filter out the one to be removed
        $urls = collect($shipment->tracking_urls ?? [])
            ->reject(fn($url) => $url === $request->tracking_url)
            ->values() // Re-index the array
            ->all();

        $shipment->update(['tracking_urls' => $urls]);

        return back()->with('success', 'Tracking URL removed.');
    }

    /**
     * Store a new payment for a specific shipment's shipping cost.
     * Designed to be called via AJAX from a modal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPayment(Request $request, Shipment $shipment)
    {
        // --- Security Check: Only Super Admin can add payments ---
        if (!auth()->user() || !auth()->user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Super Admin can add payments.'
            ], 403);
        }

        // 1. Calculate the current due amount for validation.
        $paidAmount = $shipment->payments()->sum('amount');
        $dueAmount = $shipment->total_amount - $paidAmount;

        // 2. Validate the incoming request data.
        $validator = Validator::make($request->all(), [
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string|max:255',
            'amount' => ['required', 'numeric', 'gte:0.01', 'max:' . max(0.01, $dueAmount)],
            'note' => 'nullable|string',
            'proof' => 'nullable|file|mimes:pdf,jpg,png,jpeg,doc,docx|max:5120', // Max 5MB
        ]);

        // If validation fails, return a JSON response with the errors.
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        // 3. Prepare the data array with validated data.
        $paymentData = $validator->validated();

        // 4. Handle the file upload if a 'proof' file is present.
        if ($request->hasFile('proof')) {
            // Store the file in 'storage/app/public/shipment_proofs' and get its path.
            $filePath = $request->file('proof')->store('shipment_proofs', 'public');
            $paymentData['proof'] = $filePath;
        }

        // 5. Create the new payment record in the database.
        $shipment->payments()->create($paymentData);

        // 6. Return a successful JSON response.
        // The JavaScript will handle reloading the page to show the updated data.
        return response()->json([
            'success' => true,
            'message' => 'Payment for shipment has been added successfully!',
        ]);
    }

    /**
     * Show the order detail document page
     */
    public function orderDetailDocument(Shipment $shipment)
    {
        // Authorization: Only users with the 'Shipment' role may access this page
        // if (!auth()->user() || !auth()->user()->role || auth()->user()->role->name !== 'Shipment') {
        //     return redirect()->route('shipments.index')->with('error', 'Unauthorized. Only shipment role users can save/update complete details.');
        // }

        // Load documents and relations for shipping type/tax
        $shipment->load(['documents', 'shippingType', 'shippingTax']);

        // Load available options for the selects
        $shippingTypes = \App\Models\ShippingType::orderBy('name')->get();
        $shippingTaxes = \App\Models\ShippingTax::orderBy('name')->get();

        // Group documents by type
        $documentTypes = [
            'Invoice' => $shipment->documents->where('document_type', 'Invoice'),
            'BL' => $shipment->documents->where('document_type', 'BL'),
            'Packing List' => $shipment->documents->where('document_type', 'Packing List'),
            'Telex' => $shipment->documents->where('document_type', 'Telex'),
            'Fumigation Certificate' => $shipment->documents->where('document_type', 'Fumigation Certificate'),
            'MSDS / Safety Data' => $shipment->documents->where('document_type', 'MSDS / Safety Data'),
            'Insurance' => $shipment->documents->where('document_type', 'Insurance'),
            'Other Documents' => $shipment->documents->where('document_type', 'Other Documents'),
        ];

        return view('shipments.order-detail-document', compact('shipment', 'documentTypes', 'shippingTypes', 'shippingTaxes'));
    }

    /**
     * Upload a document for a shipment
     */
    public function uploadDocument(Request $request, Shipment $shipment)
    {
        // Authorization: Only shipment role users can upload documents
        if (!auth()->user() || !auth()->user()->role || auth()->user()->role->name !== 'Shipment') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only shipment role users can upload documents.'
            ], 403);
        }

        $request->validate([
            'document_type' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $storedName = time() . '_' . uniqid() . '.' . $extension;
            $filePath = $file->storeAs('shipment_documents', $storedName, 'public');

            $shipment->documents()->create([
                'document_type' => $request->document_type,
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'file_path' => $filePath,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No file uploaded.',
        ], 400);
    }

    /**
     * Delete a shipment document
     */
    public function deleteDocument(Shipment $shipment, ShipmentDocument $document)
    {
        // Authorization: Only shipment role users can delete documents
        if (!auth()->user() || !auth()->user()->role || auth()->user()->role->name !== 'Shipment') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only shipment role users can delete documents.'
            ], 403);
        }

        // Ensure the document belongs to this shipment
        if ($document->shipment_id !== $shipment->id) {
            return response()->json([
                'success' => false,
                'message' => 'Document does not belong to this shipment.',
            ], 403);
        }

        // Delete the file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Delete the database record
        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully!',
        ]);
    }
}
