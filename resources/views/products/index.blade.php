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
                        <table class="table datatable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Variation</th>
                                    <th>Category</th>
                                    <th>Sales Price</th>
                                    <th>Purchase Price</th>
                                    <th>Supplier</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product_list as $item)
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0);" class="fw-bold">{{ $item->name }}</a>
                                            <br>
                                            {{-- This will now display the measurement for every single row --}}
                                            <small class="text-muted">{{ $item->measurement }}</small>
                                        </td>
                                        <td class="text-center">
                                            @if ($item->is_variation)
                                                <span
                                                    style="display:inline-block; width:10px; height:10px; background-color:green; border-radius:50%;"></span>
                                            @endif
                                        </td>
                                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($item->sale_price, 2) }}€</td>
                                        <td>{{ number_format($item->purchase_price, 2) }}€</td>
                                        <td>{{ $item->supplier->user->name ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <a href="{{ route('products.show', $item->id) }}"
                                                    class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('products.edit', $item->id) }}"
                                                    class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>

                                                {{-- THIS IS THE KEY LOGIC CHANGE --}}
                                                @if ($item->is_variation)
                                                    {{-- Form to delete a SINGLE VARIATION --}}
                                                    <form
                                                        action="{{ route('product-variations.destroy', $item->variation_id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger delete-button"
                                                            title="Delete this variation">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    {{-- Form to delete the ENTIRE PRODUCT --}}
                                                    <form action="{{ route('products.destroy', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger delete-button"
                                                            title="Delete this product">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
