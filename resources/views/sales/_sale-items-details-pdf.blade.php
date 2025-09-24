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
                    <th class="text-end">Tax (%)</th>
                    <th class="text-end">Total HT</th>
                    <th class="text-end">Total TTC</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->categoryItems as $item)
                    @php
                        $taxRate = $sale->orderTax->rate ?? 0;
                        $totalPrice = $item->total_price;
                        $totalHT = $totalPrice;
                        $taxAmount = $totalHT * ($taxRate / 100);
                        $totalTTC = $totalHT + $taxAmount;
                    @endphp
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
                        <td class="text-end">{{ number_format($taxRate, 0) }}%</td>
                        <td class="text-end">${{ number_format($totalHT, 2) }}</td>
                        <td class="text-end fw-bold">${{ number_format($totalTTC, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

{{-- Display Pack Items --}}
@if ($sale->packItems->isNotEmpty())
    <h4 style="margin-top: 15px;">Pack Products</h4>

    <div class="table-responsive">
        <table class="items-table">
            <thead class="thead-light">
                <tr>
                    <th style="width: 40%;">Pack &amp; Constituent Products</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Total Price</th>
                    <th class="text-end">Tax (%)</th>
                    <th class="text-end">Total HT</th>
                    <th class="text-end">Total TTC</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->packItems as $packItem)
                    @php
                        // Main financial calculations for the pack
                        $taxRate = $sale->orderTax->rate ?? 0;
                        $totalPrice = $packItem->total_price;
                        $totalHT = $totalPrice;
                        $taxAmount = $totalHT * ($taxRate / 100);
                        $totalTTC = $totalHT + $taxAmount;
                    @endphp
                    <tr>
                        <td>
                            {{-- Main Pack Name --}}
                            <strong>{{ $packItem->pack_display_name }}</strong>

                            {{-- Detailed list of constituent items --}}
                            @if ($packItem->constituentItems->isNotEmpty())
                                <div class="mt-2 ps-3 border-start">
                                    @foreach ($packItem->constituentItems as $part)
                                        @php
                                            // Robust logic to safely get all details for each part
                                            $imageUrl = asset('public/storage/images/default_image.png');
                                            $categoryName = 'N/A';
                                            $measurement = 'N/A';
                                            $selection = $part->packProductSelectedVariation;

                                            if ($selection && $selection->product) {
                                                $categoryName = $selection->product->category->name ?? 'N/A';
                                                if ($selection->variation) {
                                                    $measurement = $selection->variation->measurement;
                                                    $image =
                                                        $selection->variation->image ??
                                                        $selection->product->product_image;
                                                } else {
                                                    $measurement = $selection->product->measurement;
                                                    $image = $selection->product->product_image;
                                                }
                                            } elseif ($part->packProduct && $part->packProduct->product) {
                                                $categoryName = $part->packProduct->product->category->name ?? 'N/A';
                                                $measurement = $part->packProduct->product->measurement;
                                                $image = $part->packProduct->product->product_image;
                                            }
                                            $imageUrl = $image
                                                ? asset('public/storage/' . $image)
                                                : asset('public/storage/images/default_image.png');
                                        @endphp
                                        {{-- Display each item with full details --}}
                                        <div class="d-flex align-items-center mb-2">
                                            {{-- 1. INCREASED IMAGE SIZE --}}
                                            <img src="{{ $imageUrl }}" alt="{{ $part->product_name }}"
                                                width="40" height="40" class="rounded me-2"
                                                style="object-fit: cover;">
                                            <div>
                                                {{-- 2. INCREASED FONT SIZE (by making it bold and removing smaller classes) --}}
                                                <strong>{{ $part->product_name }}</strong><br>
                                                <small class="text-muted">{{ $categoryName }}
                                                    ({{ $measurement }})
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        {{-- Financials for the entire pack --}}
                        <td class="text-end">{{ $packItem->quantity }}</td>
                        <td class="text-end">${{ number_format($packItem->unit_price, 2) }}</td>
                        <td class="text-end">${{ number_format($totalPrice, 2) }}</td>
                        <td class="text-end">{{ number_format($taxRate, 0) }}%</td>
                        <td class="text-end">${{ number_format($totalHT, 2) }}</td>
                        <td class="text-end fw-bold">${{ number_format($totalTTC, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
