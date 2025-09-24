@extends('layouts.app')
@section('title', 'Sales')
@push('styles')
    <style>
        .custom-label {
            font-size: 1rem;
        }

        .custom-text {
            font-size: 0.95rem;
        }

        #order-items {
            padding: 15px !important;
        }
    </style>
@endpush
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Invoice</h4>
                        <h3>Invoice #{{ $sale->invoice_number }}</h3>
                    </div>
                </div>
                <div class="page-btn">
                    <a class="btn btn-info" href="{{ route('sales.view.pdf', $sale->id) }}" target="_blank">
                        <i class="ti ti-receipt"></i> Invoice
                    </a>
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">Back to Sales List</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    {{-- TOP SUMMARY SECTION --}}
                    <div class="row">
                        <div class="col-sm-6 col-lg-3 mb-2">
                            <div class="form-group">
                                <label class="custom-label">Invoice Number</label>
                                <p><strong>{{ $sale->invoice_number }}</strong></p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-2">
                            <div class="form-group">
                                <label class="custom-label">Customer</label>
                                <p class="custom-text">{{ $sale->customer->user->name }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-2">
                            <div class="form-group">
                                <label class="custom-label">Order Date</label>
                                <p class="custom-label">{{ $sale->sales_date->format('d-m-Y H:i a') }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-2">
                            <div class="form-group">
                                <label class="custom-label">Order Taken By</label>
                                <p class="custom-label">{{ $sale->user->name }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-2">
                            <div class="form-group">
                                <label class="custom-label">Order Status</label>
                                <p class="custom-label"><span
                                        class="badge bg-info">{{ ucfirst($sale->order_status) }}</span></p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-2">
                            <div class="form-group">
                                <label class="custom-label">Payment Status</label>
                                <p class="custom-label">
                                    @if ($sale->payment_status == 'Paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($sale->payment_status == 'Deposit')
                                        <span class="badge bg-warning">Deposit</span>
                                    @else
                                        <span class="badge bg-danger">Unpaid</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- FINANCIAL SUMMARY --}}
                    <div class="row">
                        <div class="col-sm-6 col-lg-3 mb-2"><label class="custom-label">Total Amount</label>
                            <p class="custom-label">${{ number_format($sale->grand_total, 2) }}</p>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-2"><label class="custom-label">Paid Amount</label>
                            <p class="custom-label">${{ number_format($sale->paid_amount, 2) }}</p>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-2"><label class="custom-label">Due Amount</label>
                            <p class="text-danger fw-bold">${{ number_format($sale->due_amount, 2) }}</p>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-2"><label class="custom-label">Discount</label>
                            <p class="custom-label">${{ number_format($sale->discount, 2) }}</p>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-2"><label class="custom-label">Shipping</label>
                            <p class="custom-label">${{ number_format($sale->shipping, 2) }}</p>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-2"><label class="custom-label">Order Tax</label>
                            <p class="custom-label">{{ number_format($sale->orderTax->rate ?? 0, 0) }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABS SECTION --}}
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments"
                                type="button" role="tab">Payments</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="order-items-tab" data-bs-toggle="tab"
                                data-bs-target="#order-items" type="button" role="tab">Order Items</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        {{-- Payments Tab Content --}}
                        <div class="tab-pane fade" id="payments" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Payment Mode</th>
                                            <th>Note</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sale->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                                <td>{{ $payment->payment_mode }}</td>
                                                <td>{{ $payment->note }}</td>
                                                <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No payments have been recorded for
                                                    this sale.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Order Items Tab Content --}}
                        <div class="tab-pane fade show active" id="order-items" role="tabpanel">
                            @include('sales._sale-items-details', ['sale' => $sale])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
