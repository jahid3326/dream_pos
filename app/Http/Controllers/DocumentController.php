<?php

namespace App\Http\Controllers;

use App\Models\PurchaseDocument;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{

    // A helper to authorize the supplier
    private function authorizeSupplier(Purchase $purchase)
    {
        $supplier = Auth::user()->supplierProfile;
        if (!$purchase->suppliers->contains($supplier)) {
            abort(403, 'Unauthorized action.');
        }
        return $supplier;
    }

    /**
     * Show the dedicated form for uploading all documents for a purchase.
     */
    public function showUploadForm(Purchase $purchase)
    {
        $supplier = $this->authorizeSupplier($purchase);

        // Eager load documents and find the specific pivot record for this supplier
        $purchase->load('documents');
        $supplierPivotData = $purchase->suppliers->find($supplier->id)->pivot;

        return view('supplier.orders.document-upload', compact('purchase', 'supplierPivotData'));
    }

    /**
     * Handle the submission of the document upload form.
     */
    public function saveDocuments(Request $request, Purchase $purchase)
    {
        // 1. Authorize that the logged-in user is a valid supplier for this purchase.
        // This helper method also conveniently returns the supplier model.
        $supplier = $this->authorizeSupplier($purchase);

        // 2. Validate the incoming request data.
        $request->validate([
            'ready_date' => 'nullable|date_format:d/m/Y',
            'documents' => 'nullable|array',
            'documents.*' => 'required|file|mimes:pdf,jpg,png,jpeg|max:10240', // Max 10MB
        ]);

        // --- TEMPORARY DEBUGGING ---
        // \Log::info('--- Document Upload Process Started ---');
        // \Log::info('Purchase ID: ' . $purchase->id);
        // \Log::info('Supplier ID: ' . $supplier->id);
        // \Log::info('Request has files for documents: ' . ($request->hasFile('documents') ? 'Yes' : 'No'));
        // \Log::info('Submitted Document Data:', $request->file('documents', []));
        // --- END DEBUGGING ---

        try {
            DB::transaction(function () use ($request, $purchase, $supplier) {
                // 3. Update the "Ready Date" if it was submitted.



                if ($request->filled('ready_date')) {
                    $purchase->suppliers()->updateExistingPivot($supplier->id, [
                        'ready_date' => Carbon::createFromFormat('d/m/Y', $request->ready_date)->format('Y-m-d')
                    ]);
                }

                // 4. Process the uploaded files.
                if ($request->hasFile('documents')) {
                    foreach ($request->file('documents') as $documentId => $uploadedFile) {

                        // --- MORE DEBUGGING ---
                        // \Log::info("Processing Document ID: {$documentId}");

                        // --- THIS IS THE UPDATED, MORE SECURE QUERY ---
                        // Find the document record that matches the ID, the purchase ID,
                        // AND the ID of the currently authenticated supplier.
                        $document = PurchaseDocument::where('id', $documentId)
                            ->where('purchase_id', $purchase->id)
                            ->where('supplier_id', $supplier->id) // <-- CRITICAL SECURITY CHECK
                            ->first();

                        if ($document) {

                            // \Log::info("SUCCESS: Document #{$documentId} found. Proceeding with upload.");
                            // a. Delete the old file if it exists.
                            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                                Storage::disk('public')->delete($document->file_path);
                            }

                            // b. Store the new file.
                            $filePath = $uploadedFile->store('purchase_documents', 'public');

                            // c. Update the document record.
                            $document->update([
                                'file_path' => $filePath,
                                'status' => 'uploaded',
                            ]);
                        } else {
                            // THIS IS THE MOST LIKELY FAILURE POINT
                            // \Log::error("FAILURE: Document #{$documentId} NOT FOUND for Purchase #{$purchase->id} and Supplier #{$supplier->id}. Skipping file upload.");
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            \Log::error('Supplier document form save failed: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while saving. Please try again.');
        }

        // 5. Redirect back to the order details page with a success message.
        return redirect()->route('orders.details', $purchase)->with('success', 'Documents and information have been saved successfully!');
    }

    public function upload(Request $request, PurchaseDocument $document)
    {
        // --- SECURITY CHECKS ---
        $user = Auth::user();
        $supplier = $user->supplierProfile;

        // 1. Ensure the document belongs to a purchase that this supplier is part of.
        if (!$document->purchase->suppliers->contains($supplier)) {
            return back()->with('error', 'You do not have permission to upload to this document.');
        }

        // 2. Validate the uploaded file.
        $request->validate([
            'document_file' => 'required|file|mimes:pdf,jpg,png,jpeg|max:5120', // Max 5MB
        ]);

        // 3. Store the file.
        try {
            // Delete the old file if it exists
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Store the new file and get its path
            $filePath = $request->file('document_file')->store('purchase_documents', 'public');

            // 4. Update the database record.
            $document->update([
                'file_path' => $filePath,
                'status' => 'uploaded', // Update status to 'uploaded'
            ]);
        } catch (\Exception $e) {
            \Log::error('Document upload failed: ' . $e->getMessage());
            return back()->with('error', 'File could not be uploaded. Please try again.');
        }

        return back()->with('success', 'Document uploaded successfully!');
    }
}
