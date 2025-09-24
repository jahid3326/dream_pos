@php
    // We need the overall order tax, which is available on the $quote object in the edit view.
    $taxRate = $quote->orderTax->rate ?? 0;

    // Calculate initial values for this specific item row for the first page load
    $unitPrice = $item->unit_price;
    $quantity = $item->quantity;
    $totalPrice = $unitPrice * $quantity;
    $totalHT = $totalPrice; // Total Hors Taxe (Before Tax) is the same as Total Price
    $totalTTC = $totalHT * (1 + $taxRate / 100); // Total Toutes Taxes Comprises (Including Tax)
@endphp

<tr class="quote-item-row">
    {{-- These hidden inputs are CRUCIAL for the form submission and JS --}}
    <input type="hidden" name="items[{{ $index }}][quote_item_id]" value="{{ $item->id }}">

    @if ($type === 'pack')
        <input type="hidden" name="items[{{ $index }}][type]" value="pack">
        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->pack_group_option_id }}">
        <input type="hidden" name="items[{{ $index }}][name]" value="{{ $item->pack_display_name }}">
    @else
        {{-- category --}}
        <input type="hidden" name="items[{{ $index }}][type]" value="category">
        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->product_id }}">
        <input type="hidden" name="items[{{ $index }}][variation_id]"
            value="{{ $item->product_variation_id }}">
        <input type="hidden" name="items[{{ $index }}][name]" value="{{ $item->product_name }}">
    @endif
    {{-- This hidden price is the source of truth for all JS calculations --}}
    <input type="hidden" class="item-price-hidden" name="items[{{ $index }}][price]"
        value="{{ $unitPrice }}">

    {{-- Row counter --}}
    <td>{{ $index + 1 }}</td>

    {{-- Name Column --}}
    <td>
        @if ($type === 'pack')
            {{ $item->pack_display_name }}
        @else
            @php
                $measurement = $item->variation->measurement ?? ($item->product->measurement ?? 'N/A');
            @endphp
            {{ $item->product_name }} ({{ $measurement }})
        @endif
    </td>

    {{-- Quantity (The only editable field in the row) --}}
    <td>
        <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-quantity"
            value="{{ $quantity }}" min="1">
    </td>

    {{-- NEW DISPLAY-ONLY FINANCIAL COLUMNS --}}
    <td class="text-end item-price-display">${{ number_format($unitPrice, 2) }}</td>
    <td class="text-end item-total-price-display">${{ number_format($totalPrice, 2) }}</td>
    <td class="text-end item-tax-display">{{ number_format($taxRate, 0) }}%</td>
    <td class="text-end item-total-ht-display">${{ number_format($totalHT, 2) }}</td>
    <td class="text-end item-total-ttc-display fw-bold">${{ number_format($totalTTC, 2) }}</td>

    {{-- Action (Remove) Button --}}
    <td>
        <button type="button" class="btn btn-danger btn-sm remove-item-btn">&times;</button>
    </td>
</tr>
