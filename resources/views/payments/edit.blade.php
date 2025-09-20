@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Edit Payment for Invoice #{{ $sale->invoice_number }}</h1>
        <p>Max Amount Allowed for this Payment: <span
                class="text-danger fw-bold">${{ number_format($sale->due_amount, 2) }}</span></p>
        <form action="{{ route('sales.payments.update', [$sale->id, $payment->id]) }}" method="POST">
            @csrf
            @method('PUT')
            @include('payments._form', ['buttonText' => 'Update Payment'])
        </form>
    </div>
@endsection
