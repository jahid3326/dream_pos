{{-- Display Standard Category Items --}}
@if ($sale->categoryItems->isNotEmpty())
    <h4>Category Products</h4>
    <div class="table-responsive">
        <table class="items-table">
            <thead class="thead-light">
                <tr>
                    <th style="width: 20%;">Product</th>
                    <th class="text-end">Quantity</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Total Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->categoryItems as $item)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @php
                                    // Determine the image and category from either the variation or the parent product
                                    $productForInfo = $item->variation->product ?? $item->product;
                                    $image = $item->variation->image ?? $productForInfo->product_image;
                                    $imageUrl = $image
                                        ? asset('public/storage/' . $image)
                                        : asset('public/storage/images/default_image.png');
                                @endphp

                                {{-- Image --}}
                                <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" width="40"
                                    height="40" class="rounded me-2" style="object-fit: cover;">

                                {{-- Name and Category --}}
                                <div>
                                    <strong>{{ $item->product_name }}</strong><br>
                                    <small class="text-muted">{{ $productForInfo->category->name ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">${{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

{{-- Display Pack Items --}}
@if ($sale->packItems->isNotEmpty())
    <h4 class="mt-4">Pack Products</h4>

    {{-- Loop through each pack line item (e.g., "Minimalist | Option 1") --}}
    @foreach ($sale->packItems as $packItem)
        <div class="border rounded p-3 mb-3">
            {{-- Main Pack Item Info --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4 class="mb-0">Pack: {{ $packItem->pack_display_name }}</h4>
                <div class="text-end">
                    <span>Qty: {{ $packItem->quantity }}</span><br>
                    <strong>Total: ${{ number_format($packItem->total_price, 2) }}</strong>
                </div>
            </div>

            {{-- Nested Table for Constituent Parts --}}
            @if ($packItem->constituentItems->isNotEmpty())
                <div class="table-responsive">
                    <table class="items-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 20%;">Product in Pack</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packItem->constituentItems as $part)
                                @php
                                    // Determine the correct product/variation model to get details from
                                    $itemModel =
                                        $part->packProductSelectedVariation->variation ??
                                        $part->packProductSelectedVariation->product;
                                    $parentProduct =
                                        $part->packProductSelectedVariation->variation->product ?? $itemModel;

                                    if ($itemModel) {
                                        $image = $itemModel->image ?? $itemModel->product_image;
                                        $imageUrl = $image
                                            ? asset('public/storage/' . $image)
                                            : asset('public/storage/images/default_image.png');
                                    } else {
                                        $imageUrl = asset('images/default_image.png');
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $imageUrl }}" alt="{{ $part->product_name }}"
                                                width="40" height="40" class="rounded me-2"
                                                style="object-fit: cover;">
                                            <div>
                                                <strong>{{ $part->product_name }}</strong><br>
                                                <small
                                                    class="text-muted">{{ $parentProduct->category->name ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $itemModel && $itemModel->getTable() == 'product_variations' ? 'bg-info' : 'bg-secondary' }}">
                                            {{ $itemModel && $itemModel->getTable() == 'product_variations' ? 'Variation' : 'Single' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endforeach
@endif
