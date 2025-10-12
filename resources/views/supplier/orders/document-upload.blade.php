@extends('layouts.app')
@section('title', 'Upload Documents - ' . $purchase->purchase_number)

@push('styles')
    <style>
        body {
            background-color: #f8f9fa;
        }

        .page-wrapper .content {
            background-color: #f8f9fa;
            box-shadow: none;
        }

        .card {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: none;
            border-radius: 0.75rem;
        }

        .status-badge-header {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-align: center;
            border: 1px solid #ccc;
        }

        .status-waiting-review-supplier {
            background-color: #f0f2f5;
            color: #495057;
            border-color: #dee2e6;
        }

        .document-item {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .document-item:last-child {
            border-bottom: none;
        }

        .btn-upload {
            border-color: #adb5bd;
        }

        .file-name-display {
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0">Order Detail Document</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 bg-transparent p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('orders.details', $purchase) }}">Order -
                                    {{ $purchase->purchase_number }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Documents</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @php $supplierStatusReview = $purchase->suppliers->firstWhere('id', Auth::user()->supplierProfile->id)->pivot->status_review; @endphp
                    <span
                        class="status-badge-header status-{{ Str::slug($supplierStatusReview) }}">{{ ucfirst(str_replace('-', ' ', $supplierStatusReview)) }}</span>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('documents.save', $purchase) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body p-4">
                        <div class="row mb-5">
                            <div class="col-md-6"><span class="text-muted">Order Number</span>
                                <p class="fw-bold">#{{ $purchase->purchase_number }}</p>
                            </div>
                            <div class="col-md-6 text-md-end"><span class="text-muted">Date creation</span>
                                <p class="fw-bold">{{ $purchase->purchase_date->format('d-m-Y H:i a') }}</p>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h5 class="fw-bold">Ready Date <small class="fw-normal text-muted">(for pickup good)</small>
                            </h5>
                            <input type="text" name="ready_date" class="form-control" placeholder="DD/MM/YYYY"
                                style="max-width: 200px;"
                                value="{{ $supplierPivotData->ready_date ? \Carbon\Carbon::parse($supplierPivotData->ready_date)->format('d/m/Y') : '' }}">
                        </div>

                        <h5 class="fw-bold mb-4">Upload Required Document</h5>

                        @php
                            // Pre-define descriptions for clarity and re-use
                            $descriptions = [
                                'Proforma Invoice (PI)' =>
                                    'Proforma with supplier details, PO reference, and incoterms.',
                                'Packing List' =>
                                    'Per carton: dimensions, weight, units per carton; totals by SKU and shipment.',
                                'Certificat of Origin (COO)' => 'Chamber-issued or Form A / EUR.1 where applicable.',
                                'Fumigation Certificat' =>
                                    'Only required if wooden packaging is used (pallets, wood frames, crates).',
                                'MSDS / Safety Data' => 'Required for chemicals, batteries, or hazardous goods.',
                                'Insurance' =>
                                    'If covered under supplier\'s policy or requested by buyer / per incoterm.',
                                'Other Documents' =>
                                    'Test reports, Product certificates (CE, UL, etc.), Export licenses, Photos, etc.',
                            ];
                            // Filter documents for only the logged-in supplier
                            $supplierDocuments = $purchase->documents->where(
                                'supplier_id',
                                Auth::user()->supplierProfile->id,
                            );
                        @endphp

                        <div class="row">
                            @foreach ($supplierDocuments as $document)
                                <div class="col-md-6">
                                    <div class="document-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold">
                                                {{ $document->document_name }}
                                                @if ($document->is_required)
                                                    <span class="text-danger small">(require)</span>
                                                @else
                                                    <span class="text-muted small">(Optional)</span>
                                                @endif
                                            </h6>
                                            <p class="text-muted small mb-0">
                                                {{ $descriptions[$document->document_name] ?? 'Please upload the relevant file.' }}
                                            </p>

                                            {{-- Display for existing files --}}
                                            @if ($document->file_path)
                                                <div class="mt-2" id="existing-file-{{ $document->id }}">
                                                    <a href="{{ asset('storage/' . $document->file_path) }}"
                                                        target="_blank" class="text-primary small">
                                                        <i class="fas fa-file-pdf fa-fw"></i>
                                                        {{ basename($document->file_path) }}
                                                    </a>
                                                </div>
                                            @endif

                                            {{-- Display for newly selected file (via JS) --}}
                                            <div class="mt-2 file-name-display" id="file-name-display-{{ $document->id }}"
                                                style="display: none;"></div>
                                        </div>
                                        <div class="text-end">
                                            <label for="doc-{{ $document->id }}"
                                                class="btn btn-outline-primary btn-upload">Upload File</label>
                                            <input type="file" id="doc-{{ $document->id }}"
                                                name="documents[{{ $document->id }}]" class="d-none file-input"
                                                data-display-target="#file-name-display-{{ $document->id }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 my-4">
                    <button type="submit" class="btn btn-primary px-4">Save</button>
                    <a href="{{ route('orders.details', $purchase) }}" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // This script provides instant feedback to the user when they select a file.
            $('.file-input').on('change', function() {
                const file = this.files[0];
                const displayTarget = $(this).data('display-target');
                const existingFile = $(displayTarget).siblings('[id^="existing-file-"]');

                if (file) {
                    // A new file was selected
                    $(displayTarget).html('<i class="fas fa-file-alt fa-fw"></i> ' + file.name).show();
                    // Hide the old file link if it exists
                    if (existingFile.length) {
                        existingFile.hide();
                    }
                } else {
                    // No file selected (e.g., user clicked cancel)
                    $(displayTarget).hide();
                    // Show the old file link again if it exists
                    if (existingFile.length) {
                        existingFile.show();
                    }
                }
            });
        });
    </script>
@endpush
