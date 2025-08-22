@extends('layouts.app')
@section('title', 'Products')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Products</h4>
                        <h6>Product Details</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('Product', 'update'))
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to List</a>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">Edit Product</a>
                    @endif
                </div>
            </div>

            <div class="row">
                {{-- Left Column: Product Details List & Variations Table --}}
                <div class="col-lg-8 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="productdetails">
                                <ul class="product-bar">
                                    <li>
                                        <h4>Product</h4>
                                        <h6>{{ $product->name }}</h6>
                                    </li>
                                    <li>
                                        <h4>Supplier</h4>
                                        <h6>{{ $product->supplier->user->name ?? 'N/A' }}</h6>
                                    </li>
                                    <li>
                                        <h4>Category</h4>
                                        <h6>{{ $product->category->name ?? 'N/A' }}</h6>
                                    </li>
                                    <li>
                                        <h4>Parent Category</h4>
                                        <h6>{{ $product->category->parent->name ?? '—' }}</h6>
                                    </li>
                                    <li>
                                        <h4>Product Type</h4>
                                        <h6>{{ ucfirst($product->type) }}</h6>
                                    </li>

                                    {{-- Display details specific to SINGLE products --}}
                                    @if ($product->type === 'single')
                                        <li>
                                            <h4>SKU</h4>
                                            <h6>{{ $product->sku }}</h6>
                                        </li>
                                        <li>
                                            <h4>Measurement</h4>
                                            <h6>{{ $product->measurement ?? 'N/A' }}</h6>
                                        </li>
                                        <li>
                                            <h4>Weight</h4>
                                            <h6>{{ $product->weight ? $product->weight . ' Kg' : 'N/A' }}</h6>
                                        </li>
                                        <li>
                                            <h4>CBM</h4>
                                            <h6>{{ $product->cbm ?? 'N/A' }}</h6>
                                        </li>
                                        <li>
                                            <h4>Tax</h4>
                                            <h6>{{ $product->tax->name ?? 'None' }} @if ($product->tax)
                                                    ({{ number_format($product->tax->rate, 2) }}%)
                                                @endif
                                            </h6>
                                        </li>
                                        <li>
                                            <h4>Purchase Price</h4>
                                            <h6>${{ number_format($product->purchase_price, 2) }}</h6>
                                        </li>
                                        <li>
                                            <h4>Margin</h4>
                                            <h6>{{ $product->margin ? number_format($product->margin, 2) . '%' : 'N/A' }}
                                            </h6>
                                        </li>
                                        <li class="fw-bold">
                                            <h4>Sale Price</h4>
                                            <h6>${{ number_format($product->sale_price, 2) }}</h6>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Product Image(s) --}}
                <div class="col-lg-4 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($product->type === 'single')
                                @php
                                    $imageUrl = $product->product_image
                                        ? asset('public/storage/' . $product->product_image)
                                        : asset('public/storage/images/default_image.png');
                                @endphp
                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="img-fluid rounded w-90">
                            @else
                                {{-- Fallback placeholder if NO variations have an image --}}
                                <img src="{{ asset('public/storage/images/default_image.png') }}" alt="Default Image"
                                    class="img-fluid rounded w-90">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- Variation Details Table (only for variation products) --}}
            @if ($product->type === 'variation' && $product->variations->isNotEmpty())
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Product Variations</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 10%;">Image</th>
                                                <th>SKU</th>
                                                <th>Measurement</th>
                                                <th>Weight</th>
                                                <th>CBM</th>
                                                <th>Tax</th>
                                                <th>Purchase Price</th>
                                                <th>Margin</th>
                                                <th class="fw-bold">Sale Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->variations as $variation)
                                                <tr>
                                                    {{-- ADDED IMAGE CELL --}}
                                                    <td>
                                                        @php
                                                            $variantImageUrl = $variation->image
                                                                ? asset('public/storage/' . $variation->image)
                                                                : asset('public/storage/images/default_image.png');
                                                        @endphp
                                                        <img src="{{ $variantImageUrl }}"
                                                            alt="{{ $variation->measurement }}" class="img-thumbnail">
                                                    </td>
                                                    <td>{{ $variation->sku }}</td>
                                                    <td>{{ $variation->measurement ?? 'N/A' }}</td>
                                                    <td>{{ $variation->weight ? $variation->weight . ' Kg' : 'N/A' }}</td>
                                                    <td>{{ $variation->cbm ?? 'N/A' }}</td>
                                                    <td>
                                                        {{ $variation->tax->name ?? 'None' }}
                                                        @if ($variation->tax)
                                                            <small
                                                                class="d-block text-muted">({{ number_format($variation->tax->rate, 2) }}%)</small>
                                                        @endif
                                                    </td>
                                                    <td>${{ number_format($variation->purchase_price, 2) }}</td>
                                                    <td>{{ $variation->margin ? number_format($variation->margin, 2) . '%' : 'N/A' }}
                                                    </td>
                                                    <td class="fw-bold">${{ number_format($variation->sale_price, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endif
        </div>
    </div>
@endsection
