@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Sales</h4>
                        <h6>Add New Sale</h6>
                    </div>
                </div>
            </div>
            <form action="{{ route('sales.store') }}" method="POST">
                @csrf
                <h4>Create Sale</h4>
                {{-- Top Section --}}
                <div class="card">
                    <div class="card-body row">
                        <div class="col-md-4"><label>Invoice Number</label><input type="text" name="invoice_number"
                                class="form-control" value="{{ $invoiceNumber }}"></div>
                        <div class="col-md-4"><label>Customer</label><select name="customer_id" class="form-select">
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label>Sales Date</label><input type="datetime-local" name="sales_date"
                                class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}"></div>
                        <div class="col-md-12 mt-3"><label>Product</label><select id="product_search"
                                class="form-select"></select></div>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th style="width: 30%;">Name</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                        <th>Tax</th>
                                        <th>Total TTC</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="sale-items-table">
                                    {{-- Rows will be added here by JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                        {{-- SubTotal display moved here --}}
                        <div class="row justify-content-end mt-3">
                            <div class="col-md-4 text-end">
                                <h5 class="fw-bold">SubTotal: $<span id="subTotalDisplay">0.00</span></h5>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom Section (Simplified) --}}
                <div class="row mt-4">
                    {{-- Left Side: Terms and Notes --}}
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Terms & Conditions</label>
                                    <textarea name="terms_and_conditions" class="form-control" rows="4"></textarea>
                                </div>
                                <div>
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side: Status and Financial Summary --}}
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Order Status</label>
                                    <select name="order_status" class="form-select">
                                        <option value="delivered">Delivered</option>
                                        <option value="pending" selected>Pending</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Order Tax</label>
                                    <div class="input-group">
                                        <select name="order_tax_id" id="order_tax_select" class="form-select">
                                            <option value="" data-rate="0">None</option>
                                            @foreach ($taxes as $tax)
                                                <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}">
                                                    {{ $tax->name }} ({{ $tax->rate }}%)</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-outline-primary" type="button">+</button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Discount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="discount" id="discountInput"
                                            class="form-control" value="0">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Shipping</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="shipping" id="shippingInput"
                                            class="form-control" value="0">
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between"><span>Order Tax</span><span>$<span
                                            id="orderTaxAmountDisplay">0.00</span></span></div>
                                <div class="d-flex justify-content-between"><span>Discount</span><span>$<span
                                            id="discountDisplay">0.00</span></span></div>
                                <div class="d-flex justify-content-between"><span>Shipping</span><span>$<span
                                            id="shippingDisplay">0.00</span></span></div>
                                <div class="d-flex justify-content-between fw-bold mt-2">
                                    <h5>Grand Total</h5>
                                    <h5>$<span id="grandTotalDisplay">0.00</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden fields for totals --}}
                <input type="hidden" name="sub_total" id="sub_total_hidden">
                <input type="hidden" name="order_tax_amount" id="order_tax_amount_hidden">
                <input type="hidden" name="grand_total" id="grand_total_hidden">

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let itemIndex = {{ isset($sale) ? $sale->items->count() : 0 }};

            // Initialize Select2 for product search
            $('#product_search').select2({
                placeholder: 'Search for a product by name or SKU...',
                minimumInputLength: 2, // Don't search until at least 2 characters are typed
                ajax: {
                    url: "{{ route('products.search') }}",
                    dataType: 'json',
                    delay: 250, // Wait 250ms after the user stops typing

                    // This function formats the data sent to the server
                    data: function(params) {
                        return {
                            q: params.term // 'params.term' is the search term from Select2
                        };
                    },

                    // This function formats the data received from the server
                    processResults: function(data) {
                        // console.log(data);
                        // Select2 expects an object with a 'results' key
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });

            // Event listener for when a product is selected
            $('#product_search').on('select2:select', function(e) {
                var data = e.params.data;
                if (data.id) {
                    addItemRow(data);
                }
                $(this).val(null).trigger('change');
            });

            function addItemRow(product) {
                const taxes = {!! Js::from($taxes->pluck('rate', 'id')) !!};

                // --- THIS IS THE FIX ---
                // 1. Get the tax rate (which might be a string).
                let rawTaxRate = product.tax_id && taxes[product.tax_id] ? taxes[product.tax_id] : 0;
                // 2. Convert it to a floating-point number.
                let taxRate = parseFloat(rawTaxRate) || 0;
                // --- END OF FIX ---

                let unitPrice = parseFloat(product.price) || 0;
                let quantity = 1;
                let totalPrice = unitPrice * quantity;
                let totalTTC = totalPrice + (totalPrice * taxRate / 100);

                let rowHtml = `
                <tr class="sale-item-row" data-index="${itemIndex}">
                    <td>${itemIndex + 1}</td>
                    <td>
                        ${product.text}
                        <input type="hidden" name="items[${itemIndex}][product_id]" value="${product.id}">
                        <input type="hidden" name="items[${itemIndex}][product_variation_id]" value="${product.variation_id || ''}">
                        <input type="hidden" name="items[${itemIndex}][product_name]" value="${product.text}">
                    </td>
                    <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control item-quantity" value="${quantity}" min="1"></td>
                    <td><input type="number" step="0.01" name="items[${itemIndex}][unit_price]" class="form-control item-price" value="${unitPrice.toFixed(2)}"></td>
                    <td><input type="text" name="items[${itemIndex}][total_price_display]" class="form-control item-total-price" value="${totalPrice.toFixed(2)}" readonly></td>
                    <td>
                        <input type="hidden" name="items[${itemIndex}][item_tax_percent]" value="${taxRate}">
                        ${taxRate.toFixed(2)}%
                    </td>
                    <td><input type="text" name="items[${itemIndex}][total_price]" class="form-control item-total-ttc" value="${totalTTC.toFixed(2)}" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-item-btn">&times;</button></td>
                </tr>`;
                $('#sale-items-table').append(rowHtml);
                itemIndex++;
                calculateTotals();
            }

            // Use event delegation for dynamically added rows
            $('#sale-items-table').on('click', '.remove-item-btn', function() {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            $('#sale-items-table').on('input', '.item-quantity, .item-price', function() {
                let row = $(this).closest('tr');
                let quantity = parseFloat(row.find('.item-quantity').val()) || 0;
                let price = parseFloat(row.find('.item-price').val()) || 0;
                let taxRate = parseFloat(row.find('input[name*="[item_tax_percent]"]').val()) || 0;

                let totalPrice = quantity * price;
                let totalTTC = totalPrice + (totalPrice * taxRate / 100);

                row.find('.item-total-price').val(totalPrice.toFixed(2));
                row.find('.item-total-ttc').val(totalTTC.toFixed(2));
                calculateTotals();
            });

            // --- REVERTED CALCULATION AND EVENT LISTENERS ---
            // Listen for changes on the main summary inputs as well
            $('#order_tax_select, #discountInput, #shippingInput').on('input change', calculateTotals);

            function calculateTotals() {
                let subTotal = 0;
                // Calculate SubTotal based on the pre-tax "Total Price" column
                $('.sale-item-row').each(function() {
                    let totalPrice = parseFloat($(this).find('.item-total-price').val()) || 0;
                    subTotal += totalPrice;
                });

                let taxSelect = $('#order_tax_select');
                let orderTaxRate = parseFloat(taxSelect.find('option:selected').data('rate')) || 0;
                let orderTaxAmount = (subTotal * orderTaxRate) / 100;
                let discount = parseFloat($('#discountInput').val()) || 0;
                let shipping = parseFloat($('#shippingInput').val()) || 0;

                // Grand Total is the SubTotal + Tax - Discount + Shipping
                let grandTotal = subTotal + orderTaxAmount - discount + shipping;

                // Update display spans in the bottom-right summary area
                $('#subTotalDisplay').text(subTotal.toFixed(2));
                $('#orderTaxAmountDisplay').text(orderTaxAmount.toFixed(2));
                $('#discountDisplay').text(discount.toFixed(2));
                $('#shippingDisplay').text(shipping.toFixed(2));
                $('#grandTotalDisplay').text(grandTotal.toFixed(2));

                // Update hidden inputs for form submission
                $('#sub_total_hidden').val(subTotal);
                $('#order_tax_amount_hidden').val(orderTaxAmount);
                $('#grand_total_hidden').val(grandTotal);
            }
        });
    </script>
@endpush
