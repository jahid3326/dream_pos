@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <form action="{{ route('sales.update', $sale->id) }}" method="POST">
            @csrf
            @method('PUT')
            <h4>Edit Sale</h4>
            {{-- Top Section --}}
            <div class="card">
                <div class="card-body row">
                    <div class="col-md-4"><label>Invoice Number</label><input type="text" name="invoice_number"
                            class="form-control" value="{{ old('invoice_number', $sale->invoice_number) }}"></div>
                    <div class="col-md-4"><label>Customer</label><select name="customer_id" class="form-select">
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" @selected(old('customer_id', $sale->customer_id) == $c->id)>{{ $c->user->name }}
                                </option>
                            @endforeach
                        </select></div>
                    <div class="col-md-4"><label>Sales Date</label><input type="datetime-local" name="sales_date"
                            class="form-control" value="{{ old('sales_date', $sale->sales_date->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="col-md-12 mt-3"><label>Product</label><select id="product_search"
                            class="form-select"></select></div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Tax</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="sale-items-table">
                            {{-- Populate existing items --}}
                            @foreach ($sale->items as $index => $item)
                                <tr class="sale-item-row">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $item->product_name }}
                                        <input type="hidden" name="items[{{ $index }}][id]"
                                            value="{{ $item->id }}">
                                        <input type="hidden" name="items[{{ $index }}][product_id]"
                                            value="{{ $item->product_id }}">
                                        <input type="hidden" name="items[{{ $index }}][product_variation_id]"
                                            value="{{ $item->product_variation_id }}">
                                        <input type="hidden" name="items[{{ $index }}][product_name]"
                                            value="{{ $item->product_name }}">
                                    </td>
                                    <td><input type="number" name="items[{{ $index }}][quantity]"
                                            class="form-control item-quantity" value="{{ $item->quantity }}"
                                            min="1"></td>
                                    <td><input type="number" name="items[{{ $index }}][unit_price]"
                                            class="form-control item-price"
                                            value="{{ number_format($item->unit_price, 2, '.', '') }}"></td>
                                    <td><input type="hidden" name="items[{{ $index }}][item_tax_percent]"
                                            value="{{ $item->item_tax_percent }}"><input type="text"
                                            name="items[{{ $index }}][item_tax_amount]"
                                            class="form-control item-tax-amount"
                                            value="{{ number_format($item->item_tax_amount, 2, '.', '') }}" readonly></td>
                                    <td><input type="text" name="items[{{ $index }}][total_price]"
                                            class="form-control item-total"
                                            value="{{ number_format($item->total_price, 2, '.', '') }}" readonly></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-item-btn">X</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- Totals display --}}
                </div>
            </div>

            {{-- Bottom Section (populate with old/sale data) --}}
            <div class="card">
                <div class="card-body row">
                    <div class="col-md-3 mt-3"><label>Order Status</label><select name="order_status" class="form-select">
                            <option value="delivered" @selected($sale->order_status == 'delivered')>Delivered</option>
                            <option value="pending" @selected($sale->order_status == 'pending')>Pending</option>
                        </select></div>
                    {{-- etc for other fields --}}
                </div>
            </div>

            {{-- Hidden fields for totals --}}
            <button type="submit" class="btn btn-primary">Update Sale</button>
        </form>
    </div>
@endsection

@push('scripts')
    {{-- The same JS from create.blade.php is needed here --}}
    {{-- Add one line to initialize calculations on page load --}}
    <script>
        $(document).ready(function() {
            let itemIndex = {{ $sale->items->count() }}; // Start index from existing items
            // ... all the JS from create.blade.php ...

            // Initial calculation on page load for edit form
            calculateTotals();
        });
    </script>
@endpush
