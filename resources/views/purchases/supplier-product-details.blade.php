@extends('layouts.app')
@section('title', 'Supplier Details for PO #' . $purchase->purchase_number)

@push('styles')
    <style>
        /* You can reuse styles from your other detail pages */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* ... add other status badge styles ... */
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4 class="fw-bold">Supplier Details for PO #{{ $purchase->purchase_number }}</h4>
                    <h6>Viewing items for: <strong>{{ $supplier->user->name }}</strong></h6>
                </div>
                <div class="page-btn">
                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary">Back to Full Purchase
                        Order</a>
                </div>
            </div>

            @include('layouts._messages')

            {{-- Supplier & Status Summary --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="text-muted">Supplier</span>
                            <p class="fw-bold">{{ $supplier->company_name }}</p>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted">Status Review</span>
                            <p><span
                                    class="status-badge status-{{ Str::slug($pivotData->status_review) }}">{{ ucfirst(str_replace('-', ' ', $pivotData->status_review)) }}</span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted">Status Production</span>
                            <p><span
                                    class="status-badge status-{{ Str::slug($pivotData->status_production) }}">{{ ucfirst($pivotData->status_production) }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- Validation Action Button --}}
                    @if ($pivotData->status_review === 'modification requested')
                        <div class="alert alert-warning d-flex justify-content-between align-items-center mt-3">
                            <span>
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Action Required:</strong> Review the proposed modifications and validate.
                            </span>
                            <form
                                action="{{ route('purchases.validateModification', ['purchase' => $purchase, 'supplier' => $supplier]) }}"
                                method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">Validate Modification</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Items Table --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Product List</h5>
                </div>
                <div class="card-body">
                    {{-- Reuse the same partial, passing only the items for this supplier --}}
                    @include('purchases._purchase-items-details', ['items' => $items])

                    <div class="d-flex justify-content-end mt-3">
                        <h4 class="fw-bold">Sub Total: ${{ number_format($supplierSubTotal, 2) }}</h4>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
