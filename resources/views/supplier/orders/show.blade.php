@extends('layouts.app')
@section('title', 'Order Summary - ' . $order->purchase_number)

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
        .status-full-payed,
        .status-paid {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-color: #2e7d32;
        }

        .status-waiting,
        .status-need-review-supplier,
        .status-modification-requested {
            background-color: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
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
                        // Get the pivot data for the logged-in supplier
                        $supplierPivot = $order->suppliers->firstWhere('id', Auth::user()->supplierProfile->id)->pivot;
                    @endphp
                    <h4 class="fw-bold mb-0">#{{ $order->purchase_number }}</h4>
                    <div class="d-flex align-items-center">
                        <span
                            class="status-badge status-{{ Str::slug($supplierPivot->status_production) }} me-3">{{ ucfirst($supplierPivot->status_production) }}</span>
                        <div class="progress" style="width: 200px; height: 8px;">
                            <div class="progress-bar {{ $progress === 100 ? 'bg-success' : '' }}" role="progressbar"
                                style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>

            {{-- Main Summary Box --}}
            <div class="card">
                <div class="card-body summary-box">
                    <div class="row align-items-center">
                        {{-- Left Side: Details --}}
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">Order Date:</div>
                                    <div class="summary-value">{{ $order->purchase_date->format('d-m-Y H:i a') }}</div>
                                </div>
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">Order Number:</div>
                                    <div class="summary-value">#{{ $order->purchase_number }}</div>
                                </div>
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">Payment Status:</div>
                                    <div class="summary-value"><span
                                            class="status-badge status-{{ Str::slug($paymentStatus) }}">{{ $paymentStatus }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">Your Total Amount:</div>
                                    <div class="summary-value">${{ number_format($supplierTotal, 2) }}</div>
                                </div>
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">Total Paid (Overall):</div>
                                    <div class="summary-value text-success">${{ number_format($paidAmount, 2) }}</div>
                                </div>
                                <div class="col-md-4 col-6 mb-4">
                                    <div class="summary-label">Total Due (Overall):</div>
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
                                        <h6 class="alert-heading">Warning Alert</h6>
                                        <p class="mb-1">Please complete the information missing.</p>
                                        <a href="#" class="btn-link text-decoration-none"
                                            data-bs-dismiss="alert">cancel</a>
                                        <a href="#documents-panel" class="btn-link text-decoration-none"
                                            onclick="document.getElementById('documents-tab').click()">open</a>
                                    </div>
                                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
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
                        <li class="nav-item" role="presentation"><button class="nav-link" id="payments-tab"
                                data-bs-toggle="tab" data-bs-target="#payments-panel" type="button">Payments</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link active" id="order-items-tab"
                                data-bs-toggle="tab" data-bs-target="#order-items-panel" type="button">Your Order
                                Items</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="documents-tab"
                                data-bs-toggle="tab" data-bs-target="#documents-panel" type="button">Document and
                                Information @if ($hasMissingInfo)
                                    <i class="fas fa-exclamation-triangle text-danger ms-1"></i>
                                @endif
                            </button></li>
                    </ul>

                    <div class="tab-content pt-4">
                        {{-- Payments Panel (Shows payments for the whole PO) --}}
                        <div class="tab-pane fade" id="payments-panel" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Payment Date</th>
                                            <th class="text-end">Amount</th>
                                            <th>Payment Method</th>
                                            <th>Proof</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($order->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                                                <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                                                <td>{{ $payment->payment_mode }}</td>
                                                <td>
                                                    @if ($payment->proof)
                                                        <a href="{{ asset('storage/' . $payment->proof) }}"
                                                            target="_blank"><i
                                                                class="far fa-file-pdf text-danger document-icon"></i></a>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No payments have been
                                                    recorded for this purchase order yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Order Items Panel (Shows only this supplier's items) --}}
                        <div class="tab-pane fade show active" id="order-items-panel" role="tabpanel">
                            @include('purchases._purchase-items-details', ['items' => $items])
                        </div>

                        {{-- Documents Panel (Interactive Upload) --}}
                        <div class="tab-pane fade" id="documents-panel" role="tabpanel">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <div class="row">
                                @foreach ($file_list as $file)
                                    <div class="col-lg-3 col-md-4 col-sm-6 text-center mb-4">
                                        <form action="{{ route('documents.upload', $file['id']) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-2">
                                                @if ($file['file_path'])
                                                    <a href="{{ asset('public/storage/' . $file['file_path']) }}"
                                                        target="_blank" class="text-decoration-none">
                                                        <i class="far fa-file-pdf text-success document-icon"></i>
                                                        <p class="mb-0 mt-1 fw-bold text-success">View {{ $file['name'] }}
                                                        </p>
                                                    </a>
                                                @else
                                                    <i class="fas fa-cloud-upload-alt text-muted document-icon"></i>
                                                    <p class="mb-0 mt-1 text-muted">{{ $file['name'] }}</p>
                                                @endif
                                            </div>
                                            @if ($file['is_required'])
                                                <span class="badge bg-primary mb-2">Required</span>
                                            @else
                                                <span class="badge bg-secondary mb-2">Optional</span>
                                            @endif
                                            <div class="input-group"><input type="file"
                                                    class="form-control form-control-sm" name="document_file"
                                                    required><button class="btn btn-outline-primary btn-sm"
                                                    type="submit">Upload</button></div>
                                            @error('document_file')
                                                <small class="text-danger d-block">{{ $message }}</small>
                                            @enderror
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                            @if ($hasMissingInfo)
                                <hr>
                                <div class="text-center">
                                    <div class="alert alert-danger"><i
                                            class="fas fa-exclamation-triangle fa-fw"></i><strong>Action Required:</strong>
                                        You have missing required documents.</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
