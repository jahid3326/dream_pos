@extends('layouts.app')
@section('content')
    <div class="container">
        {{-- Add buttons for Print, PDF, Edit etc. --}}
        <div class="card">
            <div class="card-body">
                <h4>Invoice: {{ $sale->invoice_number }}</h4>
                <p><strong>Customer:</strong> {{ $sale->customer->user->name }}</p>
                <p><strong>Date:</strong> {{ $sale->sales_date->format('d M, Y H:i') }}</p>
                <hr>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                <td>${{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-end">
                    <p>SubTotal: ${{ number_format($sale->sub_total, 2) }}</p>
                    <p>Tax: ${{ number_format($sale->order_tax_amount, 2) }}</p>
                    <p>Discount: -${{ number_format($sale->discount, 2) }}</p>
                    <p>Shipping: ${{ number_format($sale->shipping, 2) }}</p>
                    <h4>Grand Total: ${{ number_format($sale->grand_total, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
@endsection
