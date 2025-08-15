@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4 class="fw-bold">Customers</h4>
                    <h6>Customer details</h6>
                </div>
            </div>
            <div class="page-btn">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    
        <div class="card">
            <div class="card-body">
                <h3>{{ $customer->user->name }}</h3>
                <p><strong>Company:</strong> {{ $customer->company_name ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $customer->user->email }}</p>
                <p><strong>Tax Number:</strong> {{ $customer->tax_number ?? 'N/A' }}</p>
                <p><strong>Status:</strong> @if($customer->status) Enabled @else Disabled @endif</p>
                <hr>
                <h4>Billing Address</h4>
                <p>{{ $customer->billing_address ?? 'No address provided.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection