<tr class="sale-item-row">
    {{-- This hidden input tells the controller if this is an existing item --}}
    <input type="hidden" name="items[{{ $index }}][sale_item_id]" value="{{ $item->id }}">

    {{-- Row counter --}}
    <td>{{ $index + 1 }}</td>

    {{-- Name and hidden inputs for identification --}}
    <td>
        @if ($type === 'pack')
            {{ $item->pack_display_name }}
            <input type="hidden" name="items[{{ $index }}][type]" value="pack">
            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->pack_group_option_id }}">
            <input type="hidden" name="items[{{ $index }}][name]" value="{{ $item->pack_display_name }}">
        @else
            {{-- category --}}
            @php
                $measurement = 'N/A';
                if ($item->variation) {
                    $measurement = $item->variation->measurement;
                } else {
                    $measurement = $item->product->measurement;
                }
            @endphp
            {{ $item->product_name }} ({{ $measurement }})
            <input type="hidden" name="items[{{ $index }}][type]" value="category">
            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->product_id }}">
            <input type="hidden" name="items[{{ $index }}][variation_id]"
                value="{{ $item->product_variation_id }}">
            <input type="hidden" name="items[{{ $index }}][name]" value="{{ $item->product_name }}">
        @endif
        {{-- Hidden price is used for JS calculations --}}
        <input type="hidden" class="item-price-hidden" name="items[{{ $index }}][price]"
            value="{{ $item->unit_price }}">
    </td>

    {{-- Quantity and Price columns --}}
    <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control item-quantity"
            value="{{ $item->quantity }}" min="1"></td>
    <td><input type="text" class="form-control item-price-display"
            value="{{ number_format($item->unit_price, 2, '.', '') }}" readonly></td>
    <td><input type="text" class="form-control item-total-price"
            value="{{ number_format($item->total_price, 2, '.', '') }}" readonly></td>

    {{-- Action (Remove) Button --}}
    <td><button type="button" class="btn btn-danger btn-sm remove-item-btn">&times;</button></td>
</tr>
