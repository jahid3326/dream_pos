<?php

namespace App\Http\Controllers;

use App\Models\PurchaseDocument;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\DocumentFile;
use Illuminate\Support\Facades\Log;

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
        $purchase->load('documents.files');
        // $supplierPivotData = $purchase->suppliers->find($supplier->id)->pivot;
        $supplierPivotData = \App\Models\PurchaseSupplier::where('purchase_id', $purchase->id)
            ->where('supplier_id', $supplier->id)
            ->first();

        $cargo = $supplierPivotData ? $supplierPivotData->load('cargo.dimensions')->cargo : null;

        return view('supplier.orders.document-upload', compact('purchase', 'supplierPivotData', 'cargo'));
    }

    /**
     * Handle the submission of the document upload form.
     */
    public function saveDocuments(Request $request, Purchase $purchase)
    {

        // dd($request->all());

        $supplier = $this->authorizeSupplier($purchase);

        $request->validate([
            'ready_date' => 'nullable|date_format:d/m/Y',
            'documents' => 'nullable|array',
            'documents.*.*' => 'required|file|mimes:pdf,jpg,png,jpeg,doc,docx,xls,xlsx,gif,svg,csv,zip,rar',
            'packing_type' => 'nullable|string|max:255',
            'gross_weight' => 'nullable|numeric|min:0',
            'total_cbm' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'hazardous_materials' => 'nullable',
            'dimensions' => 'nullable|array',
            'dimensions.*' => ['required', 'string', 'regex:/^\d+\s*[xX]\s*\d+\s*[xX]\s*\d+$/i'],
        ]);

        try {
            DB::transaction(function () use ($request, $purchase, $supplier) {
                // Update Ready Date
                if ($request->filled('ready_date')) {
                    $purchase->suppliers()->updateExistingPivot($supplier->id, ['ready_date' => Carbon::createFromFormat('d/m/Y', $request->ready_date)->format('Y-m-d')]);
                } else {
                    $purchase->suppliers()->updateExistingPivot($supplier->id, ['ready_date' => null]);
                }

                // Handle file uploads
                if ($request->has('documents') && is_array($request->file('documents'))) {
                    // echo 'ok';
                    // exit;
                    foreach ($request->file('documents') as $documentId => $uploadedFiles) {
                        $document = PurchaseDocument::where('id', $documentId)
                            ->where('purchase_id', $purchase->id)
                            ->where('supplier_id', $supplier->id)
                            ->first();

                        if ($document) {
                            foreach ($uploadedFiles as $file) {
                                // Ensure the item is actually an uploaded file before processing
                                if ($file instanceof \Illuminate\Http\UploadedFile) {
                                    $filePath = $file->store('purchase_documents', 'public');
                                    $originalName = $file->getClientOriginalName();

                                    $document->files()->create([
                                        'file_path' => $filePath,
                                        'original_name' => $originalName,
                                    ]);
                                }
                            }
                            // Update the parent document status if it has files now
                            if ($document->files()->count() > 0 && $document->status !== 'approved') {
                                $document->update(['status' => 'uploaded']);
                            }
                        }
                    }
                }

                // 1. Find the pivot record ID
                $purchaseSupplierPivot = \App\Models\PurchaseSupplier::where('purchase_id', $purchase->id)
                    ->where('supplier_id', $supplier->id)
                    ->firstOrFail();

                $cargo = $purchaseSupplierPivot->cargo()->firstOrCreate(
                    ['purchase_supplier_id' => $purchaseSupplierPivot->id]
                );

                // Update main cargo info
                $cargo->fill($request->only([
                    'packing_type',
                    'gross_weight',
                    'total_cbm',
                    'quantity'
                ]));
                $cargo->hazardous_materials = $request->boolean('hazardous_materials');
                $cargo->save();

                // --- SYNCING LOGIC ---
                $submittedDimensions = $request->input('dimensions', []);
                $existingDimensionIds = $cargo->dimensions()->pluck('id')->toArray();

                $submittedExistingIds = [];
                $newDimensions = [];

                // 1. Separate submitted dimensions into "existing" and "new"
                foreach ($submittedDimensions as $key => $value) {
                    if (strpos($key, 'existing_') === 0) {
                        // This is an existing dimension. Extract its ID.
                        $id = substr($key, strlen('existing_'));
                        $submittedExistingIds[] = (int)$id;
                    } else {
                        // This is a new dimension to be created.
                        $newDimensions[] = $value;
                    }
                }

                // 2. Determine which dimensions to DELETE
                // These are IDs that were in the DB but NOT in the submission.
                $idsToDelete = array_diff($existingDimensionIds, $submittedExistingIds);
                if (!empty($idsToDelete)) {
                    \App\Models\CargoDimension::destroy($idsToDelete);
                }

                // 3. CREATE the new dimensions
                foreach ($newDimensions as $dimString) {
                    $dims = preg_split('/[xX]/', $dimString);
                    $dims = array_map('trim', $dims);

                    if (count($dims) === 3) {
                        $cargo->dimensions()->create([
                            // --- CAST TO INT ---
                            'length' => (int)$dims[0],
                            'width'  => (int)$dims[1],
                            'height' => (int)$dims[2],
                        ]);
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error('Supplier document save failed: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while saving. Please try again.');
        }

        return redirect()->route('documents.showUploadForm', $purchase)->with('success', 'Changes saved successfully!');
    }

    /**
     * Remove a specific uploaded file from a document.
     */
    public function destroyFile(DocumentFile $file)
    {
        // Authorize that the file's parent document belongs to the logged-in supplier
        $this->authorizeSupplier($file->purchaseDocument->purchase);

        try {
            DB::transaction(function () use ($file) {
                $purchaseDocument = $file->purchaseDocument;

                // 1. Delete the physical file from storage
                if (Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }

                // 2. Delete the database record for the file
                $file->delete();

                // 3. Check if the parent document has any other files left.
                // If not, reset its status back to 'pending'.
                if ($purchaseDocument->files()->count() === 0) {
                    $purchaseDocument->update(['status' => 'pending']);
                }
            });
        } catch (\Exception $e) {
            Log::error('Supplier document file deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Could not remove the file. Please try again.');
        }

        return back()->with('success', 'File removed successfully.');
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
            'document_file' => 'required|file|mimes:pdf,jpg,png,jpeg,doc,docx,xls,xlsx,gif,svg,csv,zip,rar', // Max 5MB
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
