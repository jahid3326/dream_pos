@extends('layouts.app')
@section('title', 'Propose Modification - ' . $purchase->purchase_number)

@push('styles')
    <style>
        /* Add specific styles for the new design */
        body {
            background-color: #f8f9fa;
        }

        .page-wrapper .content {
            background-color: #f8f9fa;
            box-shadow: none;
        }

        .card {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
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
        .status-modification-requested {
            background-color: #f0f2f5;
            color: #495057;
            border-color: #dee2e6;
        }

        .items-table {
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .items-table th {
            background-color: #f8f9fa;
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

        .form-control-sm {
            text-align: center;
        }

        .btn-danger.btn-sm {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0">Order Detail - Propose Modification</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 bg-transparent p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('orders.details', $purchase) }}">Order</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Propose Modification</li>
                        </ol>
                    </nav>
                </div>
                <div><span
                        class="status-badge-header status-{{ Str::slug($purchase->suppliers->first()->pivot->status_review) }}">{{ ucfirst(str_replace('-', ' ', $purchase->suppliers->first()->pivot->status_review)) }}</span>
                </div>
            </div>

            {{-- Form pointing to the submission route --}}
            <form action="{{ route('orders.proposeModification', $purchase) }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            <div class="col-md-6"><span class="text-muted">Invoice Number</span>
                                <p class="fw-bold fs-5">#{{ $purchase->purchase_number }}</p>
                            </div>
                            <div class="col-md-6 text-md-end"><span class="text-muted">Date creation</span>
                                <p class="fw-bold">{{ $purchase->purchase_date->format('d-m-Y H:i a') }}</p>
                            </div>
                        </div>

                        <div class="table-responsive items-table">
                            <table class="table mb-0" id="proposal-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="width: 40%;">Name</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Unit Price</th>
                                        <th class="text-end">Total Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $index => $item)
                                        @php
                                            $imageUrl = asset('public/storage/images/default_image.png'); // Default image
                                            if ($item->variation) {
                                                $image = $item->variation->image ?? $item->product->product_image;
                                                if ($image) {
                                                    $imageUrl = asset('public/storage/' . $image);
                                                }
                                            } elseif ($item->product && $item->product->product_image) {
                                                $imageUrl = asset('public/storage/' . $item->product->product_image);
                                            }
                                        @endphp
                                        <tr class="item-row">
                                            {{-- Hidden inputs to identify the item for the controller --}}
                                            <input type="hidden" name="items[{{ $index }}][id]"
                                                value="{{ $item->id }}">

                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $imageUrl }}" class="item-image me-3">
                                                    <span>{{ $item->product_name }}</span>
                                                </div>
                                            </td>
                                            <td><input type="number" class="form-control form-control-sm item-quantity"
                                                    name="items[{{ $index }}][quantity]"
                                                    value="{{ $item->quantity }}" min="0"></td>
                                            <td><input type="number" step="0.01"
                                                    class="form-control form-control-sm item-price"
                                                    name="items[{{ $index }}][unit_price]"
                                                    value="{{ $item->unit_price }}"></td>
                                            <td class="text-end item-total-price">
                                                ${{ number_format($item->total_price, 2) }}</td>
                                            <td><button type="button"
                                                    class="btn btn-danger btn-sm remove-item-btn">&times;</button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <h4 class="fw-bold" id="sub-total">Sub Total:
                                ${{ number_format($items->sum('total_price'), 2) }}</h4>
                        </div>

                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4">Submit Proposal</button>
                    <a href="{{ route('orders.details', $purchase) }}" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // This function calculates all totals for the proposal form
            function calculateTotals() {
                let subTotal = 0;
                $('#proposal-table .item-row').each(function() {
                    const row = $(this);
                    const quantity = parseFloat(row.find('.item-quantity').val()) || 0;
                    const price = parseFloat(row.find('.item-price').val()) || 0;
                    const total = quantity * price;

                    // Update the total for this row
                    row.find('.item-total-price').text('$' + total.toFixed(2));

                    // Add to the subtotal
                    subTotal += total;
                });

                // Update the final subtotal display
                $('#sub-total').text('Sub Total: $' + subTotal.toFixed(2));
            }

            // Recalculate whenever a quantity or price input changes
            $('#proposal-table').on('input', '.item-quantity, .item-price', function() {
                calculateTotals();
            });

            // Handle item removal by clicking the red 'x' button
            $('#proposal-table').on('click', '.remove-item-btn', function() {
                $(this).closest('tr').remove();
                // Recalculate totals after removing an item
                calculateTotals();
            });
        });
    </script>
@endpush
