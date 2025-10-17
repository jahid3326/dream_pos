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
                                            <img src="{{ $supplier->user->profile_picture ? asset('public/storage/' . $supplier->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                                class="rounded me-2" style="object-fit: contain" width="30"
                                                height="30">
                                            {{ $supplier->company_name }}
                                        </h5>
                                        <div>
                                            <span class="me-3">Review: <span
                                                    class="badge bg-secondary">{{ ucfirst(str_replace('-', ' ', $supplier->pivot->status_review)) }}</span></span>
                                            <span>Production: <span
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
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            {{-- Loop through each supplier involved in this purchase --}}
                            @foreach ($purchase->suppliers as $supplier)
                                @php
                                    // For each supplier, get their specific set of documents
                                    $supplierDocuments = $purchase->documents->where('supplier_id', $supplier->id);
                                    // Check if there are any required documents that are missing a file
                                    $hasMissingRequired = $supplierDocuments
                                        ->where('is_required', true)
                                        ->whereNull('file_path')
                                        ->isNotEmpty();
                                @endphp

                                <div class="mb-4 border-bottom pb-4">
                                    {{-- Supplier Header --}}
                                    <h5 class="d-flex align-items-center mb-3">
                                        <img src="{{ $supplier->user->profile_picture ? asset('public/storage/' . $supplier->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                            class="rounded me-2" style="object-fit: contain" width="30" height="30">
                                        {{ $supplier->company_name }}
                                    </h5>

                                    {{-- Document Grid --}}
                                    <div class="row">
                                        @forelse ($supplierDocuments as $doc)
                                            {{-- Only display required docs or optional ones that have been uploaded --}}
                                            @if ($doc->is_required || $doc->file_path)
                                                <div class="col-lg-2 col-md-3 col-sm-4 text-center mb-3">
                                                    @if ($doc->file_path)
                                                        <a href="{{ asset('public/storage/' . $doc->file_path) }}"
                                                            target="_blank" class="text-decoration-none">
                                                            <i class="far fa-file-pdf text-success"
                                                                style="font-size: 3rem;"></i>
                                                            <p class="mb-0 mt-1 text-dark">
                                                                {{ pathinfo($doc->document_name, PATHINFO_FILENAME) }}</p>
                                                        </a>
                                                    @else
                                                        {{-- Visually indicate a missing required document --}}
                                                        <i class="far fa-file-pdf text-danger position-relative"
                                                            style="font-size: 3rem;">
                                                            <span
                                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                                style="font-size: 0.6rem;">!</span>
                                                        </i>
                                                        <p class="mb-0 mt-1 text-danger">
                                                            {{ pathinfo($doc->document_name, PATHINFO_FILENAME) }}</p>
                                                    @endif
                                                </div>
                                            @endif
                                        @empty
                                            <p class="text-muted">No documents are tracked for this supplier.</p>
                                        @endforelse
                                    </div>

                                    {{-- Reminder Button (only shows if required files are missing) --}}
                                    @if ($hasMissingRequired)
                                        <div class="mt-3">
                                            <p class="text-danger mb-2"><i class="fas fa-exclamation-triangle fa-fw"></i>
                                                Document and information Missing</p>
                                            <form
                                                action="{{ route('purchases.sendDocumentReminder', ['purchase' => $purchase, 'supplier' => $supplier]) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">Send Reminder to
                                                    supplier</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
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
                                                        <a href="{{ asset('public/storage/' . $payment->proof) }}"
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
        });
    </script>
@endpush
