{{-- This is the content for resources/views/purchases/_documents-panel-content.blade.php --}}

@if (session('success'))
    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
@endif

{{-- Loop through each supplier involved in this purchase --}}
@foreach ($purchase->suppliers as $supplier)
    <div class="mb-5 border-bottom pb-4">
        {{-- Supplier Header --}}
        <h5 class="d-flex align-items-center mb-3">
            <img src="{{ $supplier->user->profile_picture ? asset('public/storage/' . $supplier->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                class="rounded me-2" style="object-fit: contain" width="30" height="30">
            {{ $supplier->company_name }}
        </h5>

        {{-- Document Grid for this supplier --}}
        <div class="row">
            @php
                // Get the documents and check for missing files for this specific supplier
                $supplierDocuments = $purchase->documents->where('supplier_id', $supplier->id);
                $hasMissingRequired = $supplierDocuments
                    ->where('is_required', true)
                    ->where('files', 'isEmpty')
                    ->isNotEmpty();
            @endphp

            @forelse ($supplierDocuments as $doc)
                {{-- Only display required docs or optional ones that have been uploaded --}}
                @if ($doc->is_required || $doc->files->isNotEmpty())
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 text-center mb-3">
                        @if ($doc->files->isNotEmpty())
                            <a href="#" class="text-decoration-none document-modal-trigger" data-bs-toggle="modal"
                                data-bs-target="#documentFilesModal" data-document-name="{{ $doc->document_name }}"
                                data-files-json="{{ $doc->files->map(fn($file) => ['url' => asset('public/storage/' . $file->file_path), 'name' => $file->original_name])->toJson() }}">

                                <i class="far fa-file-pdf text-success position-relative" style="font-size: 3rem;">
                                    @if ($doc->files->count() > 0)
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success"
                                            style="font-size: 1rem;">
                                            {{ $doc->files->count() }}
                                        </span>
                                    @endif
                                </i>
                                <p class="mb-0 mt-1 text-dark">{{ pathinfo($doc->document_name, PATHINFO_FILENAME) }}
                                </p>
                            </a>
                        @else
                            <i class="far fa-file-pdf text-danger position-relative" style="font-size: 3rem;">
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 0.6rem;">!</span>
                            </i>
                            <p class="mb-0 mt-1 text-danger">{{ pathinfo($doc->document_name, PATHINFO_FILENAME) }}</p>
                        @endif
                    </div>
                @endif
            @empty
                <div class="col-12">
                    <p class="text-muted">No documents are tracked for this supplier.</p>
                </div>
            @endforelse
        </div>

        {{-- Reminder Button --}}
        @if ($hasMissingRequired)
            <div class="mt-3">
                <div class="alert alert-warning d-flex justify-content-between align-items-center p-2">
                    <span class="small"><i class="fas fa-exclamation-triangle fa-fw me-1"></i> Document and information
                        Missing</span>
                    <form
                        action="{{ route('purchases.sendDocumentReminder', ['purchase' => $purchase, 'supplier' => $supplier]) }}"
                        method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">Send Reminder to supplier</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endforeach
