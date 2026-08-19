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
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .document-item:last-child {
            border-bottom: none;
        }

        .btn-upload {
            border-color: #adb5bd;
        }

        .uploaded-file-list,
        .pending-file-list {
            list-style-type: none;
            padding-left: 0;
            margin-top: 1rem;
        }

        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
        }

        .btn-remove-file {
            font-size: 1.25rem;
            line-height: 1;
            text-decoration: none;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            {{-- Header, Breadcrumb, and Status --}}
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
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="history.back()"
                        title="Back">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                    @php $supplierStatusReview = $purchase->suppliers->firstWhere('id', Auth::user()->supplierProfile->id)->pivot->status_review; @endphp
                    <span
                        class="status-badge-header status-{{ Str::slug($supplierStatusReview) }}">{{ ucfirst(str_replace('-', ' ', $supplierStatusReview)) }}</span>
                </div>
            </div>

            {{-- Session Messages & Validation Errors --}}
            @include('layouts._messages')
            @if ($errors->any())
                <div class="alert alert-danger">
                    <h6 class="alert-heading">Please fix the following errors:</h6>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Main Form for Saving Date and Uploading New Files --}}
            <form action="{{ route('documents.save', $purchase) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body p-4">
                        {{-- PO Info and Ready Date --}}
                        <div class="row mb-5">
                            <div class="col-md-4"><span class="text-muted">PO Number</span>
                                <p class="fw-bold">#{{ $purchase->purchase_number }}</p>
                            </div>
                            <div class="col-md-4"><span class="text-muted">Date creation</span>
                                <p class="fw-bold">{{ $purchase->purchase_date->format('d-m-Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Ready Date <small class="fw-normal text-muted">(for pickup
                                        good)</small></label>
                                <input type="date" id="ready_date" name="ready_date" class="form-control"
                                    value="{{ $supplierPivotData->ready_date ? \Carbon\Carbon::parse($supplierPivotData->ready_date)->format('Y-m-d') : '' }}">
                            </div>
                        </div>

                        <h5 class="fw-bold mb-4">Upload Required Document</h5>

                        @php
                            $descriptions = [
                                /* Your descriptions array */
                            ];
                            $supplierDocuments = $purchase->documents->where(
                                'supplier_id',
                                Auth::user()->supplierProfile->id,
                            );
                        @endphp

                        <div class="row">
                            @foreach ($supplierDocuments as $document)
                                <div class="col-md-6">
                                    <div class="document-item">
                                        {{-- Document Title, Description, and Upload Button --}}
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 fw-bold">{{ $document->document_name }} @if ($document->is_required)
                                                        <span class="text-danger small">(require)</span>
                                                    @else
                                                        <span class="text-muted small">(Optional)</span>
                                                    @endif
                                                </h6>
                                                <p class="text-muted small mb-0">
                                                    {{ $descriptions[$document->document_name] ?? 'Please upload the relevant file(s).' }}
                                                </p>
                                            </div>
                                            <div>
                                                <label for="doc-{{ $document->id }}"
                                                    class="btn btn-sm btn-outline-primary btn-upload">Add File(s)</label>
                                                <input type="file" id="doc-{{ $document->id }}"
                                                    name="documents[{{ $document->id }}][]" class="d-none file-input"
                                                    multiple data-preview-target="#file-preview-{{ $document->id }}">
                                            </div>
                                        </div>

                                        {{-- Container for all file lists (existing and pending) --}}
                                        <div id="file-preview-{{ $document->id }}">
                                            {{-- List of Already Uploaded Files --}}
                                            @if ($document->files->isNotEmpty())
                                                <ul class="uploaded-file-list">
                                                    @foreach ($document->files as $file)
                                                        @php
                                                            // --- GET EXTENSION DIRECTLY FROM THE FILE NAME ---
                                                            // pathinfo() is a native PHP function perfect for this.
                                                            // We use the original_name to get the true extension.
                                                            $extension = strtolower(
                                                                pathinfo($file->original_name, PATHINFO_EXTENSION),
                                                            );

                                                            $iconClass = 'fa-file'; // Default icon
                                                            $iconColor = 'text-secondary';

                                                            if (in_array($extension, ['pdf'])) {
                                                                $iconClass = 'fa-file-pdf';
                                                                $iconColor = 'text-danger';
                                                            } elseif (
                                                                in_array($extension, [
                                                                    'jpg',
                                                                    'jpeg',
                                                                    'png',
                                                                    'gif',
                                                                    'svg',
                                                                ])
                                                            ) {
                                                                $iconClass = 'fa-file-image';
                                                                $iconColor = 'text-info';
                                                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                                                $iconClass = 'fa-file-word';
                                                                $iconColor = 'text-primary';
                                                            } elseif (in_array($extension, ['xls', 'xlsx', 'csv'])) {
                                                                $iconClass = 'fa-file-excel';
                                                                $iconColor = 'text-success';
                                                            } elseif (in_array($extension, ['zip', 'rar'])) {
                                                                $iconClass = 'fa-file-archive';
                                                                $iconColor = 'text-warning';
                                                            }
                                                        @endphp

                                                        <li class="file-item" id="file-item-{{ $file->id }}">
                                                            <a href="{{ asset('storage/' . $file->file_path) }}"
                                                                target="_blank" class="text-primary text-truncate"
                                                                title="{{ $file->original_name }}">
                                                                <i
                                                                    class="fas {{ $iconClass }} {{ $iconColor }} fa-fw"></i>
                                                                {{ $file->original_name }}
                                                            </a>
                                                            {{-- This is now a simple button for AJAX --}}
                                                            <button type="button"
                                                                class="btn btn-link text-danger btn-remove-file p-0 ms-2"
                                                                data-url="{{ route('document-files.destroy', $file) }}"
                                                                data-file-id="{{ $file->id }}">
                                                                &times;
                                                            </button>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <hr class="my-5">

                <div class="row" id="cargo-section">
                    {{-- Left Side: Packing Info --}}
                    <div class="col-lg-6">
                        <h5 class="fw-bold mb-4">Packing & Cargo</h5>
                        <div class="mb-4">
                            <label class="form-label">Packing & Cargo</label>
                            <div class="btn-group w-100">
                                @php $packingTypes = ['Wood Frame', 'Pallet', 'Cardboard', 'Crate', 'Bag']; @endphp
                                @foreach ($packingTypes as $type)
                                    <input type="radio" class="btn-check" name="packing_type"
                                        id="packing-{{ Str::slug($type) }}" value="{{ $type }}"
                                        @checked($cargo?->packing_type == $type)>
                                    <label class="btn btn-outline-secondary"
                                        for="packing-{{ Str::slug($type) }}">{{ $type }}</label>
                                @endforeach
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Gross Weight (kg)</label><input
                                    type="number" step="0.01" name="gross_weight" class="form-control"
                                    placeholder="Total Kg" value="{{ $cargo?->gross_weight }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Total CBM</label><input type="number"
                                    step="1" name="total_cbm" class="form-control" placeholder="Total CBM"
                                    value="{{ number_format($cargo?->total_cbm, 2) }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Quantity <small>(package or
                                        pallet)</small></label><input type="number" name="quantity" class="form-control"
                                    placeholder="Total Pcs" value="{{ number_format($cargo?->quantity, 0) }}"></div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hazardous Materials</label>
                                <div class="form-check form-switch"><input class="form-check-input" type="checkbox"
                                        name="hazardous_materials" value="1" @checked($cargo?->hazardous_materials)></div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side: Dimensions --}}
                    <div class="col-lg-6">
                        <h5 class="fw-bold mb-4">Dimensions <small class="fw-normal text-muted">(Package or
                                palette)</small></h5>
                        <div class="input-group mb-3">
                            <input type="text" id="dimension-input" class="form-control"
                                placeholder="Length (cm) x Width (cm) x Height (cm)">
                            <button class="btn btn-outline-primary" type="button" id="add-dimension-btn">Add</button>
                        </div>
                        <div id="dimensions-list" class="row">
                            {{-- Existing dimensions will be loaded here --}}
                            @if ($cargo && $cargo->dimensions)
                                {{-- --- USE A LOOP WITH AN INDEX --- --}}
                                @foreach ($cargo->dimensions as $index => $dimension)
                                    @php
                                        $dimensionKey = 'existing_' . $dimension->id;

                                        $dimensionValue =
                                            (int) $dimension->length .
                                            'x' .
                                            (int) $dimension->width .
                                            'x' .
                                            (int) $dimension->height;
                                        $displayValue =
                                            (int) $dimension->length .
                                            ' x ' .
                                            (int) $dimension->width .
                                            ' x ' .
                                            (int) $dimension->height .
                                            ' cm';
                                    @endphp
                                    <div class="col-6 dimension-item mb-2">
                                        <div class="d-flex align-items-center bg-light p-2 rounded border">
                                            <input type="hidden" name="dimensions[{{ $dimensionKey }}]"
                                                value="{{ $dimensionValue }}">

                                            {{-- --- DISPLAY THE NUMBER --- --}}
                                            <span class="fw-bold me-2">{{ $index + 1 }} /</span>
                                            <span class="flex-grow-1">{{ $displayValue }}</span>
                                            <button type="button" class="btn-close btn-sm remove-dimension-btn"></button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 me-2 mb-5">
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="history.back()"
                        title="Back">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    <a href="{{ route('orders.details', $purchase) }}" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // JS for instant file preview of newly selected files
            $('.file-input').on('change', function(event) {
                const files = event.target.files;
                const previewTargetId = $(this).data('preview-target');
                const previewContainer = $(previewTargetId);

                // Remove any previous "pending" file lists for this input
                previewContainer.find('.pending-file-list').remove();

                if (files.length > 0) {
                    let pendingListHtml = '<ul class="pending-file-list">';
                    for (let i = 0; i < files.length; i++) {
                        pendingListHtml += `
                    <li class="file-item" style="background-color: #e9ecef;">
                        <span class="text-dark text-truncate">
                            <i class="fas fa-file-alt fa-fw"></i> ${files[i].name} (pending save)
                        </span>
                    </li>`;
                    }
                    pendingListHtml += '</ul>';
                    previewContainer.append(pendingListHtml);
                }
            });

            // Confirmation dialog and AJAX for the remove file button
            // Using event delegation to handle dynamically added elements if needed in the future
            $(document).on('click', '.btn-remove-file', function(event) {
                event.preventDefault();
                const button = $(this);
                const url = button.data('url');
                const fileId = button.data('file-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to permanently remove this file?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST', // Use POST to spoof DELETE
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                $(`#file-item-${fileId}`).fadeOut(300, function() {
                                    $(this).remove();
                                });
                                // Optionally show a small success toast instead of a big alert
                                // toastr.success('File removed.');
                            },
                            error: function(xhr) {
                                Swal.fire('Error!',
                                    'Could not remove the file. Please try again.',
                                    'error');
                            }
                        });
                    }
                });
            });


            let dimensionCounter = $('#dimensions-list .dimension-item').length;

            // Add new dimension
            $('#add-dimension-btn').on('click', function() {
                const input = $('#dimension-input');
                const value = input.val().trim();

                // Basic validation for LxWxH format
                if (value && /^\d+(\.\d+)?\s*x\s*\d+(\.\d+)?\s*x\s*\d+(\.\d+)?$/i.test(value)) {
                    dimensionCounter++;
                    const cleanValue = value.replace(/\s/g, ''); // Remove spaces
                    const displayValue = cleanValue.replace(/x/g, ' x ') + ' cm';

                    const newItem = `
                        <div class="col-6 dimension-item mb-2">
                            <div class="d-flex align-items-center bg-light p-2 rounded border">
                                <input type="hidden" name="dimensions[]" value="${cleanValue}">
                                
                                <span class="fw-bold me-2">${dimensionCounter} /</span>
                                <span class="flex-grow-1">${displayValue}</span>
                                <button type="button" class="btn-close btn-sm remove-dimension-btn"></button>
                            </div>
                        </div>`;
                    $('#dimensions-list').append(newItem);
                    input.val(''); // Clear input
                } else {
                    alert('Please enter dimensions in a valid format (e.g., 30x40x100)');
                }
            });

            // Remove dimension
            $('#dimensions-list').on('click', '.remove-dimension-btn', function() {
                $(this).closest('.dimension-item').remove();
                // Renumber items
                $('#dimensions-list .dimension-item').each(function(index) {
                    $(this).find('.fw-bold').text((index + 1) + ' /');
                });
                dimensionCounter = $('#dimensions-list .dimension-item').length;
            });

            // ready_date is a native date input now (ISO yyyy-mm-dd)
        });
    </script>
@endpush
