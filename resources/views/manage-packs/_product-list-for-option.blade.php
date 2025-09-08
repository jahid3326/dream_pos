<div id="productList-{{ $option->id }}">
    @if ($option->products->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Supplier</th>
                        <th>Variation</th>
                        <th>Action</th>
                        <th style="width: 5%;">Order</th>
                    </tr>
                </thead>
                {{-- Add data-option-id for the sortable script --}}
                <tbody class="product-sortable" data-option-id="{{ $option->id }}">
                    @foreach ($option->products as $product)
                        {{-- Add data-id for sorting --}}
                        <tr data-id="{{ $product->id }}">
                            <td>
                                @php
                                    $imageUrl = $product->product_image
                                        ? asset('public/storage/' . $product->product_image)
                                        : asset('public/storage/images/default_image.png');
                                @endphp
                                <label class="d-flex align-items-center">
                                    {{-- Product Image --}}
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="rounded me-2"
                                        width="40" height="40" style="object-fit: cover;">
                                    {{-- Product Name and Measurement --}}
                                    <div>
                                        <span class="fw-bold">{{ $product->name }}</span><br>
                                        <small class="text-muted">{{ $product->measurement ?? 'N/A' }}</small>
                                    </div>
                                </label>
                            </td>
                            <td>{{ $product->sku ?? $product->type }}</td>
                            <td>{{ $product->supplier->user->name ?? 'N/A' }}</td>
                            <td>
                                @if ($product->type === 'variation')
                                    <button class="btn btn-sm variant-btn"
                                        data-pack-product-id="{{ $product->pivot->id }}">
                                        <i class="ti ti-checkbox"></i>
                                        {{-- Variants
                                        ({{ $product->variations->count() }}) --}}
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-info disabled" disabled>
                                        <i class="ti ti-checkbox"></i>
                                        {{-- Variants
                                        ({{ $product->variations->count() }}) --}}
                                    </button>
                                @endif
                            </td>
                            <td>
                                {{-- Button to remove a single product --}}
                                <button class="me-2 p-2 d-flex align-items-center border rounded remove-product-btn"
                                    data-product-id="{{ $product->id }}" data-option-id="{{ $option->id }}">
                                    <i data-feather="trash-2" class="feather-trash-2"></i>
                                </button>
                            </td>
                            <td>
                                <i class="fas fa-bars handle" style="cursor: move;"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-muted text-center">No products assigned.</p>
    @endif
</div>
<div class="mt-2">
    <button class="btn btn-success btn-sm add-product-btn" data-option-id="{{ $option->id }}">+ Add Product</button>
    @if ($option->products->isNotEmpty())
        <button class="btn btn-outline-danger btn-sm remove-all-btn" data-option-id="{{ $option->id }}">Remove
            All</button>
    @endif
</div>
