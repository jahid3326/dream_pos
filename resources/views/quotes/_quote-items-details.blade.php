{{-- Display Standard Category Items --}}
@if ($quote->categoryItems->isNotEmpty())
    <h6>Category Products</h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
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
                @foreach ($quote->categoryItems as $item)
                    @php
                        $taxRate = $quote->orderTax->rate ?? 0;
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

{{-- Display Pack Items --}}
@if ($quote->packItems->isNotEmpty())
    <h6 class="mt-4">Pack Products</h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
                <tr>
                    <th style="width: 20%;">Pack &amp; Constituent Products</th>
                    <th class="text-end">Quantity</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Total Price</th>
                    <th class="text-end">Tax (%)</th>
                    <th class="text-end">Total HT</th>
                    <th class="text-end">Total TTC</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($quote->packItems as $packItem)
                    @php
                        $taxRate = $quote->orderTax->rate ?? 0;
                        $totalPrice = $packItem->total_price;
                        $totalHT = $totalPrice;
                        $taxAmount = $totalHT * ($taxRate / 100);
                        $totalTTC = $totalHT + $taxAmount;
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $packItem->pack_display_name }}</strong>
                            @if ($packItem->constituentItems->isNotEmpty())
                                <div class="mt-2 ps-3 border-start">
                                    @foreach ($packItem->constituentItems as $part)
                                        @php
                                            // Logic to get details for each part
                                            $imageUrl = asset('public/storage/images/default_image.png');
                                            $categoryName = 'N/A';
                                            $measurement = 'N/A';
                                            $selection = $part->packProductSelectedVariation;
                                            if ($selection && $selection->product) {
                                                $categoryName = $selection->product->category->name ?? 'N/A';
                                                $measurement =
                                                    $selection->variation->measurement ??
                                                    ($selection->product->measurement ?? 'N/A');
                                                $image =
                                                    $selection->variation->image ?? $selection->product->product_image;
                                            } elseif ($part->packProduct && $part->packProduct->product) {
                                                $categoryName = $part->packProduct->product->category->name ?? 'N/A';
                                                $measurement = $part->packProduct->product->measurement;
                                                $image = $part->packProduct->product->product_image;
                                            }
                                            $imageUrl = $image
                                                ? asset('public/storage/' . $image)
                                                : asset('public/storage/images/default_image.png');
                                        @endphp
                                        <div class="d-flex align-items-center mb-2">
                                            <img src="{{ $imageUrl }}" alt="{{ $part->product_name }}"
                                                width="40" height="40" class="rounded me-2"
                                                style="object-fit: cover;">
                                            <div>
                                                <strong>{{ $part->product_name }}</strong><br>
                                                <small class="text-muted">{{ $categoryName }}
                                                    ({{ $measurement }})</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
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
