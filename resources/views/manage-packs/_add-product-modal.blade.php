<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Products to Option</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="current-option-id">

                {{-- START: NEW FILTER SECTION --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="modal-product-search" class="form-control"
                            placeholder="Search by Product Name...">
                    </div>
                    <div class="col-md-6">
                        <select id="modal-category-filter" class="form-select">
                            <option value="">Filter by Category...</option>
                            @foreach ($childCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- END: NEW FILTER SECTION --}}

                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table" id="modal-product-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="check-all-products"></th>
                                <th>Name</th>
                                <th>SKU / Type</th>
                                {{-- We need the category ID for filtering --}}
                                <th class="d-none">Category ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allProducts as $product)
                                {{-- Add data-category-id to each row --}}
                                <tr data-category-id="{{ $product->category_id }}">
                                    <td><input type="checkbox" class="product-checkbox" id="product-{{ $product->id }}"
                                            value="{{ $product->id }}">
                                    </td>
                                    <td class="product-name">
                                        @php
                                            $imageUrl = $product->product_image
                                                ? asset('public/storage/' . $product->product_image)
                                                : asset('public/storage/images/default_image.png');
                                        @endphp
                                        <label class="form-check-label d-flex align-items-center"
                                            for="product-{{ $product->id }}" style="cursor: pointer">
                                            {{-- Product Image --}}
                                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                                class="rounded me-2" width="40" height="40"
                                                style="object-fit: cover;">
                                            {{-- Product Name and Measurement --}}
                                            <div>
                                                <span class="fw-bold">{{ $product->name }}</span><br>
                                                <small class="text-muted">{{ $product->measurement ?? 'N/A' }}</small>
                                            </div>
                                        </label>
                                    </td>
                                    <td>{{ $product->display_sku }}</td>
                                    <td class="d-none">{{ $product->category_id }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary me-1 d-flex align-items-center gap-1"
                    id="save-products-btn"><i class="ti ti-device-floppy"></i>Apply</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
