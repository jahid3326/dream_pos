@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4 class="fw-bold">Suppliers</h4>
                    <h6>Supplier details</h6>
                </div>
            </div>
            <div class="page-btn">
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    
        <div class="card">
            <div class="card-body">
                <h3>{{ $supplier->user->name }}</h3>
                <p><strong>Company:</strong> {{ $supplier->company_name ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $supplier->user->email }}</p>
                <p><strong>Tax Number:</strong> {{ $supplier->tax_number ?? 'N/A' }}</p>
                <p><strong>Status:</strong> @if($supplier->status) Enabled @else Disabled @endif</p>
                <hr>
                <h4>Billing Address</h4>
                <p>{{ $supplier->billing_address ?? 'No address provided.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection