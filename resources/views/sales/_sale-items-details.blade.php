<h5>Sale Items</h5>

{{-- Display Standard Category Items --}}
@if ($sale->categoryItems->isNotEmpty())
    <h6>Category Products</h6>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Unit Price</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->categoryItems as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-end">{{ $item->quantity }}</td>
                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">${{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

{{-- Display Pack Items --}}
@if ($sale->packItems->isNotEmpty())
    <h6 class="mt-3">Pack Products</h6>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Pack Name</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Unit Price</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->packItems as $item)
                <tr>
                    <td>
                        <strong>{{ $item->pack_display_name }}</strong>
                        {{-- List the constituent parts of the pack --}}
                        <ul class="list-unstyled ps-3 pt-1 mb-0">
                            @foreach ($item->constituentItems as $part)
                                <li><small class="text-muted">- {{ $part->product_name }}</small></li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-end">{{ $item->quantity }}</td>
                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">${{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
