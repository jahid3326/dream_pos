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

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        {{-- Left Column: Image and General Info --}}
                        <div class="col-md-4">
                            <div class="aspect-ratio-box aspect-ratio-box--275-183 mb-3">
                                @php
                                    // Define the path to your default image
                                    $imageUrl = $product->product_image
                                        ? asset('public/storage/' . $product->product_image)
                                        : asset('public/storage/images/default_image.png');
                                @endphp
                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="img-fluid rounded mb-3">
                            </div>
                            <h4 class="fw-bold">{{ $product->name }}</h4>
                            <p class="text-muted">{{ $product->category->name ?? 'N/A' }}</p>
                            <hr>
                            <p><strong>Supplier:</strong> {{ $product->supplier->user->name ?? 'N/A' }}</p>
                            <p><strong>Product Type:</strong> <span
                                    class="badge bg-info">{{ ucfirst($product->type) }}</span>
                            </p>
                        </div>

                        {{-- Right Column: Specific Details --}}
                        <div class="col-md-8">
                            {{-- Display details for a SINGLE product --}}
                            @if ($product->type === 'single')
                                <h5>Details</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 30%;">SKU</th>
                                        <td>{{ $product->sku }}</td>
                                    </tr>
                                    <tr>
                                        <th>Measurement</th>
                                        <td>{{ $product->measurement ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Weight</th>
                                        <td>{{ $product->weight ? $product->weight . ' Kg' : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>CBM</th>
                                        <td>{{ $product->cbm ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                                <h5 class="mt-4">Pricing</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 30%;">Purchase Price</th>
                                        <td>${{ number_format($product->purchase_price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Margin</th>
                                        <td>{{ $product->margin ? $product->margin . '%' : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tax</th>
                                        <td>{{ $product->tax->name ?? 'None' }} ({{ $product->tax->rate ?? 0 }}%)</td>
                                    </tr>
                                    <tr>
                                        <th>Sale Price</th>
                                        <td class="fw-bold">${{ number_format($product->sale_price, 2) }}</td>
                                    </tr>
                                </table>
                            @endif

                            {{-- Display details for a VARIATION product --}}
                            @if ($product->type === 'variation')
                                <h5>Variations</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                {{-- Add a column for the variation image --}}
                                                <th style="width: 10%;">Image</th>
                                                <th>SKU</th>
                                                <th>Measurement</th>
                                                <th>Purchase Price</th>
                                                <th>Margin</th>
                                                <th>Tax</th> {{-- <-- ADD NEW HEADER --}}
                                                <th class="fw-bold">Sale Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($product->variations as $variation)
                                                <tr>
                                                    <td>
                                                        @php
                                                            // Use the same logic to fall back to a default image
                                                            $variationImageUrl = $variation->image
                                                                ? asset('public/storage/' . $variation->image)
                                                                : asset('public/storage/images/default_image.png');
                                                        @endphp
                                                        <img src="{{ $variationImageUrl }}" alt="Variation Image"
                                                            class="img-thumbnail" width="80">
                                                    </td>
                                                    <td>{{ $variation->sku }}</td>
                                                    <td>{{ $variation->measurement ?? 'N/A' }}</td>
                                                    <td>${{ number_format($variation->purchase_price, 2) }}</td>
                                                    <td>{{ $variation->margin ? $variation->margin . '%' : 'N/A' }}
                                                    </td>

                                                    {{-- ADD NEW CELL TO DISPLAY TAX INFO --}}
                                                    <td>
                                                        {{ $variation->tax->name ?? 'None' }}
                                                        @if ($variation->tax)
                                                            <small
                                                                class="d-block text-muted">({{ number_format($variation->tax->rate, 2) }}%)</small>
                                                        @endif
                                                    </td>

                                                    <td class="fw-bold">${{ number_format($variation->sale_price, 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                {{-- Update colspan to match the new number of columns --}}
                                                <tr>
                                                    <td colspan="7" class="text-center">No variations found for this
                                                        product.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
