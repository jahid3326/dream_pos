@extends('layouts.app')
@section('title', 'Order Details')

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4 class="fw-bold">Order Details</h4>
                    <h3>#{{ $order->purchase_number }}</h3>
                </div>
                <div class="page-btn">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>

            {{-- Summary Card --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <span class="text-muted">PO Number</span>
                            <p class="fw-bold">{{ $order->purchase_number }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <span class="text-muted">Date</span>
                            <p class="fw-bold">{{ $order->purchase_date->format('d M, Y') }}</p>
                        </div>
                        @if ($order->sale)
                            <div class="col-md-4 mb-3">
                                <span class="text-muted">Original Sale Reference</span>
                                <p class="fw-bold">{{ $order->sale->invoice_number }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Items List --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Your Items</h5>
                </div>
                <div class="card-body">
                    @include('purchases._purchase-items-details', ['items' => $items])
                </div>
            </div>

            {{-- Financials and Documents --}}
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Required Documents</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @foreach ($file_list as $file)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $file['name'] }}
                                        <span class="badge {{ $file['status'] == 'Ok' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $file['status'] }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-3">
                                <button class="btn btn-primary">Upload Documents</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Your Financial Summary</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between"><span>Your Total Amount:</span>
                                    <strong>${{ number_format($supplierTotal, 2) }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between"><span>Amount Paid:</span> <strong
                                        class="text-success">${{ number_format($paidAmount, 2) }}</strong></li>
                                <li class="list-group-item d-flex justify-content-between"><span>Amount Due:</span> <strong
                                        class="text-danger">${{ number_format($dueAmount, 2) }}</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
