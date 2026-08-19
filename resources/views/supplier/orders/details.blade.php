@extends('layouts.app')
@section('title', 'Order Detail - ' . $purchase->purchase_number)

@push('styles')
    <style>
        /* Custom styles for the clean, modern order detail page */
        body {
            background-color: #f8f9fa;
        }

        .page-wrapper .content {
            background-color: #f8f9fa;
            box-shadow: none;
            padding: 1.5rem;
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

        .status-waiting-review-supplier,
        .status-pending,
        .status-modification-requested {
            background-color: #f0f2f5;
            color: #495057;
            border-color: #dee2e6;
        }

        .status-complet {
            background-color: #d4edda;
            color: #155724;
            border-color: #155724;
        }

        .btn-outline-secondary {
            border-color: #ced4da;
        }

        .items-table-wrapper {
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .items-table thead th {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: 500;
            border-bottom: 1px solid #e9ecef;
        }

        .items-table td,
        .items-table th {
            padding: 1rem;
            vertical-align: middle;
        }

        .item-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            background-color: #e9ecef;
            border-radius: 0.25rem;
        }

        .breadcrumb {
            margin-bottom: 0;
            background-color: transparent;
            padding: 0;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            {{-- Header: Title, Status, and Breadcrumb --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0">Order Detail</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Order</li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $purchase->purchase_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="history.back()"
                        title="Back">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                    @php
                        // Get the status for the logged-in supplier from the pivot table
                        $supplierStatusReview = $purchase->suppliers->firstWhere(
                            'id',
                            Auth::user()->supplierProfile->id,
                        )->pivot->status_review;
                    @endphp
                    <span
                        class="status-badge-header status-{{ Str::slug($supplierStatusReview) }}">{{ ucfirst(str_replace('-', ' ', $supplierStatusReview)) }}</span>
                </div>
            </div>

            @include('layouts._messages')

            {{-- Order Info & Items Table --}}
            <div class="card">
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6"><span class="text-muted">Order Number</span>
                            <p class="fw-bold">#{{ $purchase->purchase_number }}</p>
                        </div>
                        <div class="col-md-6 text-md-end"><span class="text-muted">Date creation</span>
                            <p class="fw-bold">{{ $purchase->purchase_date->format('d-m-Y H:i a') }}</p>
                        </div>
                    </div>

                    <div class="table-responsive items-table-wrapper">
                        <table class="table mb-0 items-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="width: 40%;">Name</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total Price</th>
                                    <th class="text-end">CBM</th>
                                    <th class="text-end">Total CBM</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $index => $item)
                                    @php
                                        $imageUrl = asset('storage/images/default_image.png');
                                        $unit_cbm = 0;
                                        if ($item->variation) {
                                            $unit_cbm = $item->variation->cbm ?? 0;
                                            $image = $item->variation->image ?? $item->product->product_image;
                                            if ($image) {
                                                $imageUrl = asset('storage/' . $image);
                                            }
                                        } elseif ($item->product) {
                                            $unit_cbm = $item->product->cbm ?? 0;
                                            if ($item->product->product_image) {
                                                $imageUrl = asset('storage/' . $item->product->product_image);
                                            }
                                        }
                                        $total_cbm = $item->quantity * $unit_cbm;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center"><img src="{{ $imageUrl }}"
                                                    class="item-image me-3"><span>{{ $item->product_name }}</span></div>
                                        </td>
                                        <td class="text-end">{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end">${{ number_format($item->total_price, 2) }}</td>
                                        <td class="text-end">{{ number_format($unit_cbm, 2) }}</td>
                                        <td class="text-end">{{ number_format($total_cbm, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <h4 class="fw-bold">Sub Total: ${{ number_format($supplierSubTotal, 2) }}</h4>
                    </div>
                </div>
            </div>

            {{-- Document Requirement Card (only shows if required documents are missing) --}}
            {{-- @if ($hasMissingDocuments) --}}
            <div class="card mt-4" id="document-requirement-card">
                <div class="card-body text-center p-4">
                    <h5 class="card-title">Complete Document and or information requirement for Shipping</h5>
                    <div class="mt-3">
                        {{-- --- THIS IS THE UPDATED LINK --- --}}
                        <a href="{{ route('documents.showUploadForm', $purchase) }}" class="btn btn-primary px-4">Now</a>
                        <button type="button" class="btn btn-outline-secondary px-4" id="btn-later">Later</button>
                    </div>
                </div>
            </div>
            {{-- @endif --}}

            <div class="alert alert-info text-center mt-4">
                This order has already been processed. Current status:
                <strong>{{ ucfirst(str_replace('-', ' ', $supplierStatusReview)) }}</strong>
            </div>
            {{-- Action Buttons --}}
            <div class="d-flex justify-content-center gap-2 mt-4">
                <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#confirmOrderModal">Confirm
                    Order</button>
                <a href="{{ route('orders.showModificationForm', $purchase) }}"
                    class="btn btn-outline-secondary px-4">Propose Modification</a>
                <form action="{{-- route('orders.reject', $purchase) --}}#" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary px-4">Reject</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Order Modal -->
    <div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-labelledby="confirmOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    @if ($hasMissingDocuments)
                        <h5 class="mb-3" id="confirmOrderModalLabel">Complete Document and or information requirement for
                            Shipping</h5>
                        <p class="text-muted">You can confirm the order now and upload the required documents later from the
                            order summary page.</p>
                    @else
                        <h5 class="mb-3" id="confirmOrderModalLabel">Confirm this Purchase Order?</h5>
                        <p class="text-muted">Once confirmed, the order will move to the next stage.</p>
                    @endif
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <form action="{{ route('orders.confirm', $purchase) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary px-4">Yes, Confirm</button>
                        </form>
                        <button type="button" class="btn btn-outline-secondary px-4"
                            data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#btn-later').on('click', function() {
                $('#document-requirement-card').fadeOut();
            });
        });
    </script>
@endpush
