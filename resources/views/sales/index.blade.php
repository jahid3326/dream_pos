@extends('layouts.app')
@section('title', 'Sales')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Sales List</h4>
                        <h6>Manage your Sales</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('Sale', 'create'))
                        <a href="{{ route('sales.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add
                            New Sale</a>
                    @endif
                </div>
            </div>

            @include('layouts._messages')
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Sales Date</th>
                                    <th>Status</th>
                                    <th>Grand Total</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $sale->customer->user->name }}</td>
                                        <td>{{ $sale->sales_date->format('d M, Y') }}</td>
                                        <td>
                                            <span
                                                class="badge 
                                    @if ($sale->order_status == 'delivered') bg-success
                                    @elseif($sale->order_status == 'pending') bg-warning
                                    @else bg-danger @endif">
                                                {{ ucfirst($sale->order_status) }}
                                            </span>
                                        </td>
                                        <td>${{ number_format($sale->grand_total, 2) }}</td>
                                        <td class="text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <a href="{{ route('sales.show', $sale->id) }}"
                                                    class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('sales.edit', $sale->id) }}"
                                                    class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                                <form action="{{ route('sales.destroy', $sale->id) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger delete-button"><i
                                                            class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $sales->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
