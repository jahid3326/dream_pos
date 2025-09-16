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
                                    $image = $item->variation->image ?? $item->product->product_image;
                                    $imageUrl = $image
                                        ? asset('public/storage/' . $image)
                                        : asset('public/storage/images/default_image.png');

                                    $measurement = 'N/A';
                                    if ($item->variation) {
                                        $measurement = $item->variation->measurement;
                                    } else {
                                        $measurement = $item->product->measurement;
                                    }
                                @endphp

                                {{-- Image --}}
                                <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" width="40"
                                    height="40" class="rounded me-2" style="object-fit: cover;">

                                {{-- Name and Category --}}
                                <div>
                                    <strong>{{ $item->product_name }}</strong><br>
                                    <small
                                        class="text-muted">{{ $item->product->category->name ?? 'N/A' }}({{ $measurement }})</small>
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
                                <th style="width: 50%;">Product in Pack</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packItem->constituentItems as $part)
                                <tr>
                                    {{-- THIS IS THE BULLETPROOF LOGIC --}}
                                    @php
                                        // Start with null defaults
                                        $imageUrl = asset('public/storage/images/default_image.png');
                                        $categoryName = 'N/A';
                                        $itemType = 'N/A';
                                        $measurement = 'N/A';

                                        // Safely access the pack definition record
                                        $selection = $part->packProductSelectedVariation;

                                        if ($selection) {
                                            // Check if it's a variation
    if ($selection->product) {
        $itemType = 'Variation';
        // Safely access the parent product for category
        $categoryName = $selection->product->category->name ?? 'N/A';
        if ($selection->variation) {
            $measurement = $selection->variation->measurement;
            // Safely get the image
            if ($selection->variation->image) {
                $imageUrl = asset(
                    'public/storage/' . $selection->variation->image,
                );
            } else {
                $imageUrl = asset('public/storage/images/default_image.png');
            }
        } else {
            $measurement = $selection->product->measurement;
            if ($selection->product->product_image) {
                $imageUrl = asset(
                    'public/storage/' . $selection->product->product_image,
                );
            } else {
                $imageUrl = asset('public/storage/images/default_image.png');
            }
        }
    }
} else {
    if ($part->packProduct) {
        $itemType = 'Single';
        $categoryName = $part->packProduct->product->category->name ?? 'N/A';
        $measurement = $part->packProduct->product->measurement;
        if ($part->packProduct->product->product_image) {
            $imageUrl = asset(
                'public/storage/' . $part->packProduct->product->product_image,
            );
        } else {
            $imageUrl = asset('public/storage/images/default_image.png');
                                                }
                                            }
                                        }
                                    @endphp
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $imageUrl }}" alt="{{ $part->product_name }}"
                                                width="40" height="40" class="rounded me-2"
                                                style="object-fit: cover;">
                                            <div>
                                                <strong>{{ $part->product_name }}</strong><br>
                                                <small
                                                    class="text-muted">{{ $categoryName }}({{ $measurement }})</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $itemType === 'Variation' ? 'bg-info' : 'bg-secondary' }}">
                                            {{ $itemType }}
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
