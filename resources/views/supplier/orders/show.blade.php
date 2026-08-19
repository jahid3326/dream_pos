@extends('layouts.app')
@section('title', 'Order Summary - ' . $purchase->purchase_number)

@push('styles')
    <style>
        /* Custom styles for the order summary page */
        .summary-box {
            padding: 1.5rem;
        }

        .summary-label {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .summary-value {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .progress-bar {
            background-color: #ffc107;
        }

        /* Default to orange for 'in process' */
        .progress-bar.bg-success {
            background-color: #28a745 !important;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            border: 1px solid;
            min-width: 100px;
        }

        .status-in-process,
        .status-deposit {
            background-color: #fff8e1;
            color: #ff8f00;
            border-color: #ff8f00;
        }

        .status-complet,
        .status-paid {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-color: #2e7d32;
        }

        .status-waiting,
        .status-pending {
            background-color: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
        }

        .status-unpaid {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .alert-warning-custom {
            background-color: #fff9c4;
            border: 1px solid #fbc02d;
            color: #5d4037;
        }

        .nav-tabs .nav-link.active {
            border-bottom: 3px solid #0d6efd;
            color: #0d6efd;
            background-color: transparent;
        }

        .nav-tabs .nav-link {
            border: 0;
            border-bottom: 3px solid transparent;
        }

        .document-icon {
            font-size: 2.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            {{-- Header Section --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    @php
                        $supplierPivot = $purchase->suppliers->firstWhere('id', Auth::user()->supplierProfile->id)
                            ->pivot;
                    @endphp
                    <h4 class="fw-bold mb-0">#{{ $purchase->purchase_number }}</h4>
                    <div class="d-flex align-items-center">
                        <span
                            class="status-badge status-{{ Str::slug($supplierPivot->status_production) }} me-3">{{ ucfirst($supplierPivot->status_production) }}</span>
                        <div class="progress" style="width: 200px; height: 8px;">
                            <div class="progress-bar {{ $progress === 100 ? 'bg-success' : '' }}" role="progressbar"
                                style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}"></div>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>

            @include('layouts._messages')

            {{-- Main Summary Box --}}
            <div class="card">
                <div class="card-body summary-box">
                    <div class="row align-items-center">
                        {{-- Left Side: Details --}}
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">Order Date:</div>
                                    <div class="summary-value">{{ $purchase->purchase_date->format('d-m-Y') }}</div>
                                </div>
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">PO Number:</div>
                                    <div class="summary-value">#{{ $purchase->purchase_number }}</div>
                                </div>
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">Your Payment Status:</div>
                                    <div class="summary-value">
                                        <span
                                            class="status-badge status-{{ Str::slug($paymentStatus) }}">{{ $paymentStatus }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">Status Review:</div>
                                    <div class="summary-value">
                                        <span
                                            class="status-badge status-{{ Str::slug($supplierPivot->status_review) }}">{{ ucfirst(str_replace('-', ' ', $supplierPivot->status_review)) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">Status Production:</div>
                                    <div class="summary-value">
                                        <span
                                            class="status-badge status-{{ Str::slug($supplierPivot->status_production) }}">{{ ucfirst($supplierPivot->status_production) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6 mb-4">
                                    {{-- This is an empty spacer to align the grid nicely --}}
                                </div>
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">Your Total Amount:</div>
                                    <div class="summary-value">${{ number_format($supplierTotal, 2) }}</div>
                                </div>
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">Amount Paid to You:</div>
                                    <div class="summary-value text-success">${{ number_format($paidAmount, 2) }}</div>
                                </div>
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">Amount Due to You:</div>
                                    <div class="summary-value text-danger">${{ number_format($dueAmount, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        {{-- Right Side: Warning Alert --}}
                        @if ($hasMissingInfo)
                            <div class="col-lg-4">
                                <div class="alert alert-warning-custom d-flex align-items-start" role="alert">
                                    <i class="fas fa-exclamation-triangle fa-fw me-3 mt-1" style="color: #fbc02d;"></i>
                                    <div>
                                        <h6 class="alert-heading">Action Required</h6>
                                        <p class="mb-1">You have missing required documents.</p>
                                        <a href="#" class="btn-link text-decoration-none"
                                            data-bs-dismiss="alert">dismiss</a>
                                        <a href="#documents-panel" class="btn-link text-decoration-none"
                                            onclick="document.getElementById('documents-tab').click()">view</a>
                                    </div>
                                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                                        aria-label="Close">x</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tabs Section --}}
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="orderTab" role="tablist">
                        <li class="nav-item" role="presentation"><button class="nav-link active" id="order-items-tab"
                                data-bs-toggle="tab" data-bs-target="#order-items-panel" type="button">Your Order
                                Items</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="documents-tab"
                                data-bs-toggle="tab" data-bs-target="#documents-panel" type="button">Documents &
                                Information @if ($hasMissingInfo)
                                    <i class="fas fa-exclamation-triangle text-danger ms-1"></i>
                                @endif
                            </button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="payments-tab"
                                data-bs-toggle="tab" data-bs-target="#payments-panel" type="button">Payment
                                History</button></li>
                    </ul>

                    <div class="tab-content pt-4">
                        {{-- Order Items Panel --}}
                        <div class="tab-pane fade show active" id="order-items-panel" role="tabpanel">
                            @include('purchases._purchase-items-details', ['items' => $items])
                        </div>

                        {{-- Documents Panel --}}
                        <div class="tab-pane fade" id="documents-panel" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Manage Your Documents</h5>
                                <a href="{{ route('documents.showUploadForm', $purchase) }}"
                                    class="btn btn-sm btn-primary">Go to Full Document Page</a>
                            </div>

                            @if ($hasMissingInfo)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle fa-fw"></i>
                                    <strong>Action Required:</strong> You have missing required documents. Please use the
                                    full document management page to upload them.
                                </div>
                            @endif

                            <div class="row">
                                @forelse ($file_list as $doc)
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 text-center mb-3">

                                        @if ($doc->files->isNotEmpty())
                                            {{-- File(s) exist: Create a modal trigger with data attributes --}}
                                            <a href="#" class="text-decoration-none document-modal-trigger"
                                                data-bs-toggle="modal" data-bs-target="#documentFilesModal"
                                                data-document-name="{{ $doc->document_name }}"
                                                data-files-json="{{ json_encode($doc->files->map(fn($file) => ['url' => asset('storage/' . $file->file_path), 'name' => $file->original_name])) }}">

                                                <i class="fas fa-file-alt text-success position-relative"
                                                    style="font-size: 3rem;">
                                                    <span
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success"
                                                        style="font-size: 1rem;">
                                                        {{ $doc->files->count() }}
                                                    </span>
                                                </i>
                                                <p class="mb-0 mt-1 text-dark">
                                                    {{ pathinfo($doc->document_name, PATHINFO_FILENAME) }}</p>
                                            </a>
                                        @else
                                            {{-- No files exist --}}
                                            <i class="fas fa-file-alt {{ $doc->is_required ? 'text-danger' : 'text-muted' }} position-relative"
                                                style="font-size: 3rem;">
                                                @if ($doc->is_required)
                                                    <span
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                        style="font-size: 0.6rem;">!</span>
                                                @endif
                                            </i>
                                            <p class="mb-0 mt-1 {{ $doc->is_required ? 'text-danger' : 'text-muted' }}">
                                                {{ pathinfo($doc->document_name, PATHINFO_FILENAME) }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <p class="text-muted">No documents are tracked for this order.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Payments Panel --}}
                        <div class="tab-pane fade" id="payments-panel" role="tabpanel">
                            @php
                                // Filter the entire purchase's payments to get only those for the logged-in supplier.
$supplierPayments = $purchase->payments->where(
    'supplier_id',
    Auth::user()->supplierProfile->id,
);

// Calculate the total paid to THIS supplier.
$totalPaidToSupplier = $supplierPayments->sum('amount');

                                // Calculate the amount due for THIS supplier.
                                $dueForSupplier = $supplierTotal - $totalPaidToSupplier;
                            @endphp

                            {{-- 1. NEW: Supplier-Specific Financial Summary --}}
                            <div class="card bg-light border mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Your Payment Summary</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <span class="summary-label">Your Total Order Amount</span>
                                            <p class="summary-value">${{ number_format($supplierTotal, 2) }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="summary-label">Total Paid to You</span>
                                            <p class="summary-value text-success">
                                                ${{ number_format($totalPaidToSupplier, 2) }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="summary-label">Amount Due to You</span>
                                            <p class="summary-value text-danger">${{ number_format($dueForSupplier, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. UPDATED: Payment History Table --}}
                                <h5 class="mx-3 mb-3">Your Payment History</h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Payment Date</th>
                                                <th class="text-end">Amount</th>
                                                <th>Payment Method</th>
                                                <th>Note</th>
                                                <th>Proof</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Loop through only the supplier-specific payments --}}
                                            @forelse($supplierPayments as $payment)
                                                <tr>
                                                    <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                                                    <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                                                    <td>{{ $payment->payment_mode }}</td>
                                                    <td>{{ $payment->note }}</td>
                                                    <td>
                                                        @if ($payment->proof)
                                                            @php
                                                                // --- LOGIC TO DETERMINE THE ICON ---
                                                                $extension = strtolower(
                                                                    pathinfo($payment->proof, PATHINFO_EXTENSION),
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
                                                                } elseif (
                                                                    in_array($extension, ['xls', 'xlsx', 'csv'])
                                                                ) {
                                                                    $iconClass = 'fa-file-excel';
                                                                    $iconColor = 'text-success';
                                                                }
                                                            @endphp
                                                            <a href="{{ asset('storage/' . $payment->proof) }}"
                                                                target="_blank" title="{{ basename($payment->proof) }}">
                                                                <i
                                                                    class="fas {{ $iconClass }} {{ $iconColor }} fa-lg"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        You have not received any payments for this order yet.
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
    </div>
    <!-- Document Files Modal -->
    <div class="modal fade" id="documentFilesModal" tabindex="-1" aria-labelledby="documentFilesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentFilesModalLabel">Uploaded Files</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group" id="document-file-list">
                        {{-- JavaScript will populate this list --}}
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
            // --- SCRIPT FOR DOCUMENT FILES MODAL (WITH DYNAMIC ICONS) ---

            function getFileIcon(filename) {
                const extension = filename.split('.').pop().toLowerCase();
                let iconClass = 'fa-file',
                    iconColor = 'text-secondary';
                const types = {
                    'text-danger': ['pdf'],
                    'text-info': ['jpg', 'jpeg', 'png', 'gif', 'svg'],
                    'text-primary': ['doc', 'docx'],
                    'text-success': ['xls', 'xlsx', 'csv'],
                    'text-warning': ['zip', 'rar']
                };
                const icons = {
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
                    'rar': 'fa-file-archive'
                };
                if (icons[extension]) {
                    iconClass = icons[extension];
                    for (const color in types) {
                        if (types[color].includes(extension)) {
                            iconColor = color;
                            break;
                        }
                    }
                }
                return `fas ${iconClass} ${iconColor} fa-fw me-2`;
            }

            $('.document-modal-trigger').on('click', function(event) {
                event.preventDefault();
                const button = $(this);
                const documentName = button.data('document-name');
                const files = button.data('files-json');

                const modal = $('#documentFilesModal');
                const fileListContainer = modal.find('#document-file-list');

                modal.find('#documentFilesModalLabel').text('Files for ' + documentName);
                fileListContainer.empty();

                if (files && files.length > 0) {
                    files.forEach(function(file) {
                        const iconClasses = getFileIcon(file.name);
                        const listItemHtml = `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-truncate" title="${file.name}">
                            <i class="${iconClasses}"></i>${file.name}
                        </span>
                        <a href="${file.url}" target="_blank" class="btn btn-sm btn-outline-primary">Preview</a>
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
