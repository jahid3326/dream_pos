@extends('layouts.app')
@section('title', 'Purchase Details')

@push('styles')
    <style>
        .summary-label {
            font-weight: 600;
            color: #6c757d;
        }

        .summary-value {
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            border: 1px solid transparent;
        }

        .status-ordered {
            background-color: #ffe8d1;
            color: #ff8f00;
            border-color: #ff8f00;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
            border-color: #155724;
        }

        .status-partial {
            background-color: #ffe8d1;
            color: #ff8f00;
            border-color: #ff8f00;
        }

        .status-unpaid {
            background-color: #fff0f0;
            color: #f44336;
            border-color: #f44336;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Purchase Order Details</h4>
                        <h3>#{{ $purchase->purchase_number }}</h3>
                    </div>
                </div>
                <div class="page-btn">
                    <a href="{{ route('purchases.print', $purchase) }}" class="btn btn-light" id="print-po-btn"><i
                            class="fas fa-print me-2"></i> Print</a>
                    <a href="{{ route('purchases.downloadPdf', $purchase) }}" class="btn btn-primary"><i
                            class="fas fa-download me-2"></i> Download PDF</a>
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>

            {{-- Purchase Summary Card --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-12 mb-3">
                            <span class="summary-label">Order Number</span>
                            <p class="summary-value">{{ $purchase->purchase_number }}</p>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12 mb-3">
                            <span class="summary-label">PO Date</span>
                            <p class="summary-value">{{ $purchase->purchase_date->format('d M, Y') }}</p>
                        </div>
                        @if ($purchase->sale)
                            <div class="col-lg-3 col-sm-6 col-12 mb-3">
                                <span class="summary-label">Original Sale</span>
                                <p class="summary-value"><a
                                        href="{{ route('sales.show', $purchase->sale->id) }}">{{ $purchase->sale->invoice_number }}</a>
                                </p>
                            </div>
                        @endif
                        <div class="col-lg-3 col-sm-6 col-12 mb-3">
                            <span class="summary-label">Status</span>
                            <p><span
                                    class="status-badge status-{{ Str::slug($purchase->status) }}">{{ ucfirst($purchase->status) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs for Details --}}
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="purchaseTab" role="tablist">
                        <li class="nav-item" role="presentation"><button class="nav-link active" id="items-tab"
                                data-bs-toggle="tab" data-bs-target="#items-panel" type="button">Items & Suppliers</button>
                        </li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="documents-tab"
                                data-bs-toggle="tab" data-bs-target="#documents-panel" type="button">Documents</button>
                        </li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="payments-tab"
                                data-bs-toggle="tab" data-bs-target="#payments-panel" type="button">Payments</button></li>
                    </ul>

                    <div class="tab-content pt-3">
                        {{-- Items & Suppliers Panel --}}
                        <div class="tab-pane fade show active" id="items-panel" role="tabpanel">
                            @foreach ($purchase->suppliers as $supplier)
                                <div class="mb-4">
                                    <div
                                        class="d-flex justify-content-between align-items-center bg-light p-2 rounded mb-2">
                                        <h5 class="d-flex align-items-center mb-0">
                                            <img src="{{ $supplier->user->profile_picture ? asset('storage/' . $supplier->user->profile_picture) : asset('storage/images/default_avatar.png') }}"
                                                class="rounded me-2" style="object-fit: contain" width="30"
                                                height="30">
                                            {{ $supplier->company_name }}
                                        </h5>
                                        <div class="d-flex align-items-center">
                                            <span class="me-3">Review: <span
                                                    class="badge bg-secondary">{{ ucfirst(str_replace('-', ' ', $supplier->pivot->status_review)) }}</span></span>
                                            <span class="me-3">Production: <span
                                                    class="badge bg-info">{{ ucfirst($supplier->pivot->status_production) }}</span></span>
                                        </div>
                                    </div>
                                    @php
                                        $itemsForThisSupplier = $itemsBySupplier[$supplier->id] ?? collect();
                                    @endphp
                                    @include('purchases._purchase-items-details', [
                                        'items' => $itemsForThisSupplier,
                                    ])
                                </div>
                            @endforeach
                        </div>

                        {{-- Documents Panel --}}
                        <div class="tab-pane fade" id="documents-panel" role="tabpanel">
                            @include('purchases._documents-panel-content', ['purchase' => $purchase])
                        </div>

                        {{-- Payments Panel --}}
                        <div class="tab-pane fade" id="payments-panel" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="mb-0 summary-label">Total Amount: <span
                                            class="text-dark fw-bold">${{ number_format($purchase->total_amount, 2) }}</span>
                                    </p>
                                    <p class="mb-0 summary-label">Amount Paid: <span
                                            class="text-success fw-bold">${{ number_format($purchase->paid_amount, 2) }}</span>
                                    </p>
                                    <p class="mb-0 summary-label">Amount Due: <span
                                            class="text-danger fw-bold">${{ number_format($purchase->due_amount, 2) }}</span>
                                    </p>
                                </div>
                                <button class="btn btn-primary">Add Payment</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Supplier</th>
                                            <th>Mode</th>
                                            <th>Note</th>
                                            <th>Proof</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($purchase->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                                <td>
                                                    {{-- Safely access the supplier's name. Show 'N/A' if not linked. --}}
                                                    {{ $payment->supplier->company_name ?? 'N/A' }}
                                                </td>
                                                <td>{{ $payment->payment_mode }}</td>
                                                <td>{{ $payment->note }}</td>
                                                <td>
                                                    @if ($payment->proof)
                                                        <a href="{{ asset('storage/' . $payment->proof) }}"
                                                            target="_blank" class="text-primary" title="View Proof">
                                                            <i class="far fa-file-pdf fa-lg"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">None</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No payments recorded
                                                    yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Document Files Modal -->
    <div class="modal fade" id="documentFilesModal" tabindex="-1" aria-labelledby="documentFilesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentFilesModalLabel">Uploaded Files for [Document Name]</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- The list of files will be injected here by JavaScript --}}
                    <ul class="list-group" id="document-file-list">
                        {{-- JS will populate this --}}
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // Handle the print button to open in a new window
            $('#print-po-btn').on('click', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const printWindow = window.open(url, '_blank');
                if (printWindow) {
                    printWindow.focus();
                } else {
                    alert('Please allow pop-ups for this site to print the purchase order.');
                }
            });

            // Helper function to get the correct icon class based on a filename
            function getFileIcon(filename) {
                // Get the extension and convert to lower case
                const extension = filename.split('.').pop().toLowerCase();

                let iconClass = 'fa-file'; // Default icon
                let iconColor = 'text-secondary';

                const fileTypes = {
                    'text-danger': ['pdf'],
                    'text-info': ['jpg', 'jpeg', 'png', 'gif', 'svg'],
                    'text-primary': ['doc', 'docx'],
                    'text-success': ['xls', 'xlsx', 'csv'],
                    'text-warning': ['zip', 'rar']
                };

                const iconMap = {
                    'pdf': 'fa-file-pdf',
                    'jpg': 'fa-file-image',
                    'jpeg': 'fa-file-image',
                    'png': 'fa-file-image',
                    'gif': 'fa-file-image',
                    'svg': 'fa-file-image',
                    'doc': 'fa-file-word',
                    'docx': 'fa-file-word',
                    'xls': 'fa-file-excel',
                    'xlsx': 'fa-file-excel',
                    'csv': 'fa-file-excel',
                    'zip': 'fa-file-archive',
                    'rar': 'fa-file-archive',
                };

                if (iconMap[extension]) {
                    iconClass = iconMap[extension];
                    for (const color in fileTypes) {
                        if (fileTypes[color].includes(extension)) {
                            iconColor = color;
                            break;
                        }
                    }
                }

                return `fas ${iconClass} ${iconColor} fa-fw me-2`;
            }

            // Listen for clicks on any element with the 'document-modal-trigger' class
            $('.document-modal-trigger').on('click', function(event) {
                event.preventDefault();

                const button = $(this);
                const documentName = button.data('document-name');
                const files = button.data('files-json');

                const modal = $('#documentFilesModal');
                const fileListContainer = modal.find('#document-file-list');

                // 1. Update the modal's title
                modal.find('#documentFilesModalLabel').text('Uploaded Files for ' + documentName);

                // 2. Clear any old file list items
                fileListContainer.empty();

                // 3. Build and append the new file list
                if (files && files.length > 0) {
                    files.forEach(function(file) {
                        // --- THIS IS THE KEY CHANGE ---
                        // Call our helper function to get the dynamic icon class string
                        const iconClasses = getFileIcon(file.name);

                        const listItemHtml = `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-truncate" title="${file.name}">
                            <i class="${iconClasses}"></i>
                            ${file.name}
                        </span>
                        <a href="${file.url}" target="_blank" class="btn btn-sm btn-outline-primary">
                            Preview
                        </a>
                    </li>`;
                        fileListContainer.append(listItemHtml);
                    });
                } else {
                    fileListContainer.html('<li class="list-group-item text-muted">No files found.</li>');
                }
            });
        });
    </script>
@endpush
