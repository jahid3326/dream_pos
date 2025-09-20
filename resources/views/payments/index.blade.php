@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1>Payments for Invoice #{{ $sale->invoice_number }}</h1>
                <a href="{{ route('sales.index') }}">← Back to Sales List</a>
            </div>
            @if ($sale->due_amount > 0)
                <a href="{{ route('sales.payments.create', $sale->id) }}" class="btn btn-primary">+ Add New Payment</a>
            @endif
        </div>
        @include('layouts._messages')

        {{-- Summary Bar --}}
        <div class="card mb-3">
            <div class="card-body d-flex justify-content-around text-center">
                <div>
                    <h5>Total Amount</h5>
                    <p class="h4">${{ number_format($sale->grand_total, 2) }}</p>
                </div>
                <div>
                    <h5>Paid Amount</h5>
                    <p class="h4 text-success">${{ number_format($sale->paid_amount, 2) }}</p>
                </div>
                <div>
                    <h5>Due Amount</h5>
                    <p class="h4 text-danger">${{ number_format($sale->due_amount, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Payment Mode</th>
                            <th>Note</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sale->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                <td>{{ $payment->payment_mode }}</td>
                                <td>{{ $payment->note }}</td>
                                <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('sales.payments.edit', [$sale->id, $payment->id]) }}"
                                            class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('sales.payments.destroy', [$sale->id, $payment->id]) }}"
                                            method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-danger delete-button">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No payments recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
