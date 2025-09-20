@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Edit Payment</h4>
                    <h6>For Invoice <a href="{{ route('sales.show', $sale->id) }}">#{{ $sale->invoice_number }}</a></h6>
                </div>
            </div>
            @include('layouts._messages')
            <p><strong>Max Amount for this Payment:</strong> <span
                    class="text-danger fw-bold">${{ number_format($sale->max_editable_amount, 2) }}</span></p>

            <form action="{{ route('payments.update', $payment->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('payments._form', ['buttonText' => 'Update Payment'])
            </form>
        </div>
    </div>
@endsection
