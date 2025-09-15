@extends('layouts.app')
@section('title', 'Sales')
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
                    <button class="btn btn-info" onclick="window.print()">Print</button>
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">Back to Sales List</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Billed To:</h5>
                            <p class="mb-0">{{ $sale->customer->user->name }}</p>
                            <p class="mb-0">{{ $sale->customer->billing_address }}</p>
                            <p class="mb-0">{{ $sale->customer->user->email }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0"><strong>Sale Date:</strong> {{ $sale->sales_date->format('F j, Y') }}</p>
                            <p class="mb-0"><strong>Order Status:</strong> {{ ucfirst($sale->order_status) }}</p>
                            <p class="mb-0"><strong>Sold By:</strong> {{ $sale->user->name }}</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Item Description</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $counter = 1; @endphp
                                {{-- Loop through standard category items --}}
                                @foreach ($sale->categoryItems as $item)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td class="text-end">{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end">${{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach

                                {{-- Loop through pack items --}}
                                @foreach ($sale->packItems as $item)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>
                                            <strong>{{ $item->pack_display_name }}</strong>
                                            {{-- Optionally list the constituent parts --}}
                                            <ul class="list-unstyled ps-3 pt-1">
                                                @foreach ($item->constituentItems as $part)
                                                    <li><small class="text-muted">- {{ $part->product_name }}</small></li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="text-end">{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end">${{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4 justify-content-end">
                        <div class="col-md-5 text-end">
                            <p class="mb-1"><strong>Sub Total:</strong> ${{ number_format($sale->sub_total, 2) }}</p>
                            @if ($sale->order_tax_amount > 0)
                                <p class="mb-1"><strong>Tax ({{ $sale->orderTax->name ?? '' }}):</strong>
                                    ${{ number_format($sale->order_tax_amount, 2) }}</p>
                            @endif
                            @if ($sale->discount > 0)
                                <p class="mb-1 text-danger"><strong>Discount:</strong>
                                    -${{ number_format($sale->discount, 2) }}</p>
                            @endif
                            @if ($sale->shipping > 0)
                                <p class="mb-1"><strong>Shipping:</strong> ${{ number_format($sale->shipping, 2) }}</p>
                            @endif
                            <hr>
                            <h4 class="fw-bold">Grand Total: ${{ number_format($sale->grand_total, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
