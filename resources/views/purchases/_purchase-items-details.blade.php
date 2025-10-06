<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <thead class="thead-light">
            <tr>
                <th style="width: 40%;">Product</th>
                <th class="text-end">Quantity</th>
                <th class="text-end">Unit Price</th>
                <th class="text-end">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                @php
                    $imageUrl = asset('public/storage/images/default_image.png');
                    $categoryName = $item->product->category->name ?? 'N/A';
                    $measurement = 'N/A';

                    if ($item->variation) {
                        $measurement = $item->variation->measurement;
                        $image = $item->variation->image ?? $item->product->product_image;
                        $imageUrl = $image ? asset('public/storage/' . $image) : $imageUrl;
                    } else {
                        $measurement = $item->product->measurement;
                        $imageUrl = $item->product->product_image
                            ? asset('public/storage/' . $item->product->product_image)
                            : $imageUrl;
                    }
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{ $imageUrl }}" class="rounded me-2" width="40" height="40"
                                style="object-fit: cover;">
                            <div>
                                <strong>{{ $item->product_name }}</strong><br>
                                <small class="text-muted">{{ $categoryName }} ({{ $measurement }})</small>
                            </div>
                        </div>
                    </td>
                    <td class="text-end">{{ $item->quantity }} pcs</td>
                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end fw-bold">${{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
