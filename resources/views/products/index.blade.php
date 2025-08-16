@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Products</h4>
                        <h6>Manage your products</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('Product', 'create'))
                        <a href="{{ route('products.create') }}" class="btn btn-primary"><i
                                class="ti ti-circle-plus me-1"></i>Add New Product</a>
                        <a href="#" class="btn btn-secondary">Import Products</a>
                    @endif
                </div>
            </div>
            @include('layouts._messages')
            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('products.index') }}" method="GET" class="row g-3 align-items-center">
                        <div class="col-md-4"><input type="text" name="search" class="form-control"
                                placeholder="Search by name..." value="{{ request('search') }}"></div>
                        <div class="col-md-3"><select name="supplier_id" class="form-select">
                                <option value="">All Suppliers</option>
                                @foreach ($suppliers as $s)
                                    <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>{{ $s->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3"><select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}
                                    </option>
                                @endforeach
                            </select></div>
                        <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Filter</button></div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                    <div class="search-set">
                        <div class="search-input">
                            <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable" id="customer-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Variations</th>
                                    <th>Sale Price</th>
                                    <th>Purchase Price</th>
                                    <th>Supplier</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $product->name }}<br><small
                                                class="text-muted">{{ $product->measurement }}</small></td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>{{ $product->type === 'variation' ? $product->variations->count() : '—' }}</td>
                                        <td>{{ $product->type === 'single' ? '$' . number_format($product->sale_price, 2) : '(Multiple)' }}
                                        </td>
                                        <td>{{ $product->type === 'single' ? '$' . number_format($product->purchase_price, 2) : '(Multiple)' }}
                                        </td>
                                        <td>{{ $product->supplier->user->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
