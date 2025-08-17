@extends('layouts.app')
@section('title', 'Products')
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
                        <a href="{{ route('products.import.show') }}" class="btn btn-secondary">Import Products</a>
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
                                    <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>{{ $s->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    {{-- Render the parent category --}}
                                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                        {{ $category->name }}
                                    </option>

                                    {{-- If the parent has children, loop through and render them with indentation --}}
                                    @if ($category->children->isNotEmpty())
                                        @foreach ($category->children as $child)
                                            <option value="{{ $child->id }}" @selected(request('category_id') == $child->id)>
                                                &nbsp;&nbsp;&nbsp;› {{ $child->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>
                        </div>
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
                                    @if (hasActionPermission('Product', 'update') || hasActionPermission('Product', 'delete'))
                                        <th class="no-sort"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product_list as $item)
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
                                        <td>${{ number_format($item->sale_price, 2) }}</td>
                                        <td>${{ number_format($item->purchase_price, 2) }}</td>
                                        <td>{{ $item->supplier->company_name ?? 'N/A' }}</td>
                                        @if (hasActionPermission('Product', 'update') || hasActionPermission('Product', 'delete'))
                                            <td class="text-end">
                                                <div class="d-flex gap-0 justify-content-end">
                                                    @if (hasActionPermission('Product', 'show'))
                                                        <a href="{{ route('products.show', $item->id) }}"
                                                            class="me-2 p-2 d-flex align-items-center border rounded"><i
                                                                data-feather="eye" class="feather-eye"></i></a>
                                                    @endif
                                                    @if (hasActionPermission('Product', 'update'))
                                                        <a href="{{ route('products.edit', $item->id) }}"
                                                            class="me-2 p-2 d-flex align-items-center border rounded"><i
                                                                data-feather="edit" class="feather-edit"></i></a>
                                                    @endif
                                                    {{-- THIS IS THE KEY LOGIC CHANGE --}}
                                                    @if ($item->is_variation)
                                                        {{-- Form to delete a SINGLE VARIATION --}}
                                                        @if (hasActionPermission('Product', 'delete'))
                                                            <form
                                                                action="{{ route('product-variations.destroy', $item->variation_id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="me-2 p-2 d-flex align-items-center border rounded delete-button"
                                                                    title="Delete this product">
                                                                    <i data-feather="trash-2" class="feather-trash-2"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @else
                                                        {{-- Form to delete the ENTIRE PRODUCT --}}
                                                        @if (hasActionPermission('Product', 'delete'))
                                                            <form action="{{ route('products.destroy', $item->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="me-2 p-2 d-flex align-items-center border rounded delete-button"
                                                                    title="Delete this product">
                                                                    <i data-feather="trash-2" class="feather-trash-2"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
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
@push('scripts')
    <script>
        $(document).on('click', '.delete-button', function() {

            // Prevent the form from submitting immediately
            event.preventDefault();

            // Find the closest parent form of the clicked button
            const form = this.closest('form');

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!", // ✅ This works
                cancelButtonText: "Cancel",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-danger ml-1"
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        })
    </script>
@endpush
