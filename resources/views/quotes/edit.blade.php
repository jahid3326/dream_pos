@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="content">
            {{-- Make sure your form action and method are correct --}}
            <form action="{{ route('quotes.update', $quote->id) }}" method="POST">
                @csrf
                @method('PUT')
                <h4>Edit Quote #{{ $quote->quote_number }}</h4>

                {{-- Top Section: Quote Number, Customer, Date --}}
                <div class="card">
                    <div class="card-body row">
                        <div class="col-md-4"><label>Quote Number</label><input type="text" name="quote_number"
                                class="form-control" value="{{ old('quote_number', $quote->quote_number) }}" required>
                        </div>
                        <div class="col-md-4"><label>Customer</label><select name="customer_id" id="customer_select"
                                class="form-select" required>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}" @selected(old('customer_id', $quote->customer_id) == $c->id)>{{ $c->user->name }}
                                    </option>
                                @endforeach
                            </select></div>
                        <div class="col-md-4"><label>Quote Date</label><input type="datetime-local" name="quote_date"
                                class="form-control"
                                value="{{ old('quote_date', $quote->quote_date->format('Y-m-d\TH:i')) }}" required></div>
                        <div class="col-md-6 mt-3">
                            <label>Add Category Product</label>
                            <select id="product_search" class="form-select"></select>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Add Pack Option</label>
                            <select id="pack_option_search" class="form-select"></select>
                        </div>
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
                                        <th style="width: 40%;">Name</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Sub Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="quote-items-table">
                                    @php $currentIndex = 0; @endphp
                                    @foreach ($quote->categoryItems as $item)
                                        @include('quotes._cart-item-row', [
                                            'item' => $item,
                                            'type' => 'category',
                                            'index' => $currentIndex++,
                                        ])
                                    @endforeach
                                    @foreach ($quote->packItems as $item)
                                        @include('quotes._cart-item-row', [
                                            'item' => $item,
                                            'type' => 'pack',
                                            'index' => $currentIndex++,
                                        ])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- --- THIS IS THE NEWLY POSITIONED SUBTOTAL --- --}}
                        <div class="row justify-content-end mt-4">
                            <div class="col-lg-4 col-md-6">
                                <div class="d-flex justify-content-between fw-bold" style="margin-right: 132px;">
                                    <h5>SubTotal :</h5>
                                    <h5 id="subTotalDisplay">$0.00</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Bottom Section --}}
                <div class="row mt-3">
                    {{-- Left side: Empty or for Notes/Terms --}}
                    <div class="col-lg-8">
                        {{-- You can add notes/terms textareas here if you wish --}}
                    </div>

                    {{-- Right side: Order Details and Final Summary --}}
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label>Order Status</label>
                                    <select name="status" class="form-select">
                                        <option value="delivered" @selected(old('status', $quote->status) == 'delivered')>Delivered</option>
                                        <option value="on process" @selected(old('status', $quote->status) == 'on process')>On Process</option>
                                        <option value="in process" @selected(old('status', $quote->status) == 'in process')>In Process</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Order Tax</label>
                                    <div class="input-group">
                                        <select name="order_tax_id" id="order_tax_select"
                                            class="form-select calculation-trigger">
                                            <option value="" data-rate="0">None</option>
                                            @foreach ($taxes as $tax)
                                                <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}"
                                                    @selected(old('order_tax_id', $quote->order_tax_id) == $tax->id)>{{ $tax->name }} ({{ $tax->rate }}%)
                                                </option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                            data-bs-target="#order-tax-modal">+</button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Discount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        {{-- This input is for DISPLAY ONLY and is readonly --}}
                                        <input type="number" step="0.01" id="discountInputAmount" class="form-control"
                                            value="{{ number_format($quote->discount, 2, '.', '') }}" readonly>
                                        {{-- This span will show the percentage when active --}}
                                        <span class="input-group-text" id="discount-percentage-display"
                                            style="display: none;"></span>
                                        {{-- This button opens the discount modal --}}
                                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                            data-bs-target="#discount">+</button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Shipping ($)</label>
                                    <input type="number" step="0.01" name="shipping" id="shippingInput"
                                        class="form-control calculation-trigger"
                                        value="{{ old('shipping', $quote->shipping) }}">
                                </div>

                                {{-- --- THE NEW GRAND TOTAL SECTION --- --}}
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <h4>Grand Total:</h4>
                                    <h4 id="grandTotalDisplay">$0.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden fields for totals --}}
                <input type="hidden" name="sub_total" id="sub_total_hidden">
                <input type="hidden" name="order_tax_amount" id="order_tax_amount_hidden">
                <input type="hidden" name="discount_type" id="discount_type_hidden"
                    value="{{ old('discount_type', $quote->discount_type ?? 'fixed') }}">
                <input type="hidden" name="discount_value" id="discount_value_hidden"
                    value="{{ old('discount_value', $quote->discount_rate ?? $quote->discount) }}">
                {{-- This hidden input stores the final calculated amount for the controller --}}
                <input type="hidden" name="discount_amount" id="discount_amount_hidden">
                <input type="hidden" name="grand_total" id="grand_total_hidden">

                <div class="text-end my-3">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary me-1">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update
                        Quote</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Discount Modal -->
    <div class="modal fade modal-default" id="discount">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Apply Discount</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- This form will be handled by our JavaScript --}}
                <form id="discount-form">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal-discount-type">
                                <option value="fixed">Fixed Amount ($)</option>
                                <option value="percentage">Percentage (%)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Value <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="modal-discount-value" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end flex-wrap gap-2">
                        <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary">Apply Discount</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Discount Modal -->
@endsection

@push('scripts')
    {{-- Include Select2 CSS/JS if not in main layout --}}
    <script>
        $(document).ready(function() {
            // IMPORTANT: Start the index from the number of existing items
            let itemIndex = {{ $quote->categoryItems->count() + $quote->packItems->count() }};

            // Select2 Initialization
            $('#product_search').select2({
                placeholder: 'Search for a product...',
                ajax: {
                    url: "{{ route('products.search') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    }
                }
            });

            // Add Item to Table
            $('#product_search').on('select2:select', function(e) {
                var data = e.params.data;
                if (data.id) {
                    addCategoryItemRow(data); // Call the correct function
                }
                $(this).val(null).trigger('change');
            });

            /**
             * Helper function to add a CATEGORY item row to the table.
             */
            function addCategoryItemRow(productData) {
                const qty = 1;
                const price = parseFloat(productData.price) || 0;
                const totalPrice = qty * price;
                const taxRate = parseFloat(productData.tax_rate) || 0;
                const totalTTC = totalPrice * (1 + taxRate / 100);

                // Check if this item (product or variation) is already in the cart
                const cartId = `c-${productData.id}-${productData.variation_id || '0'}`;
                const existingRow = $(`#quote-items-table tr[data-cart-id="${cartId}"]`);

                if (existingRow.length > 0) {
                    // If it exists, just increment the quantity
                    const qtyInput = existingRow.find('.item-quantity');
                    qtyInput.val(parseInt(qtyInput.val()) + 1).trigger('input');
                    return;
                }

                const newRow = `
            <tr class="sale-item-row" data-cart-id="${cartId}">
                <td>${itemIndex + 1}</td>
                <td>
                    ${productData.text}
                    <input type="hidden" name="items[${itemIndex}][type]" value="category">
                    <input type="hidden" name="items[${itemIndex}][id]" value="${productData.id}">
                    <input type="hidden" name="items[${itemIndex}][variation_id]" value="${productData.variation_id || ''}">
                    <input type="hidden" name="items[${itemIndex}][name]" value="${productData.name}">
                    <input type="hidden" class="item-price-hidden" name="items[${itemIndex}][price]" value="${price}">
                    <input type="hidden" name="items[${itemIndex}][item_tax_percent]" value="${taxRate}">
                </td>
                <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control item-quantity" value="${qty}" min="1"></td>
                <td><input type="text" class="form-control item-price-display" value="${price.toFixed(2)}" readonly></td>
                <td><input type="text" class="form-control item-total-price" value="${totalPrice.toFixed(2)}" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-item-btn">&times;</button></td>
            </tr>`;

                $('#quote-items-table').append(newRow);
                itemIndex++;
                calculateTotals();
            }

            $('#pack_option_search').select2({
                placeholder: 'Search by Pack, Surface, or Option...',
                minimumInputLength: 2,
                ajax: {
                    url: "{{ route('sales.pack-options.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });

            // --- Listener for when a PACK OPTION is selected ---
            $('#pack_option_search').on('select2:select', function(e) {
                var data = e.params.data;
                if (data.id) {
                    // Check if this pack option is already in the cart
                    const alreadyExists = $(`#quote-items-table input[name$="[type]"][value="pack"]`)
                        .siblings(`input[name$="[id]"][value="${data.id}"]`)
                        .length > 0;

                    if (alreadyExists) {
                        Swal.fire('Already Added', 'This pack option is already in the list.', 'info');
                    } else {
                        addPackItemRow(data);
                    }
                }
                // Reset the search box
                $(this).val(null).trigger('change');
            });

            /**
             * Helper function to add a PACK item row to the table.
             */
            function addPackItemRow(packData) {
                const qty = 1;
                const price = parseFloat(packData.price) || 0;
                const totalPrice = qty * price;

                // Use the new _cart-item-row partial logic
                const newRow = `
            <tr class="sale-item-row">
                <td>${itemIndex + 1}</td>
                <td>
                    ${packData.name_for_cart}
                    <input type="hidden" name="items[${itemIndex}][type]" value="pack">
                    <input type="hidden" name="items[${itemIndex}][id]" value="${packData.id}">
                    <input type="hidden" name="items[${itemIndex}][name]" value="${packData.name_for_cart}">
                    <input type="hidden" class="item-price-hidden" name="items[${itemIndex}][price]" value="${price}">
                </td>
                <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control item-quantity" value="${qty}" min="1"></td>
                <td><input type="text" class="form-control item-price-display" value="${price.toFixed(2)}" readonly></td>
                <td><input type="text" class="form-control item-total-price" value="${totalPrice.toFixed(2)}" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-item-btn">&times;</button></td>
            </tr>`;

                $('#quote-items-table').append(newRow);
                itemIndex++;
                calculateTotals();
            }

            // Remove Item from Table
            $('#quote-items-table').on('click', '.remove-item-btn', function() {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            // Recalculate when quantity or price changes
            $('#quote-items-table').on('input', '.item-quantity', function() {
                const row = $(this).closest('tr');
                const qty = parseFloat($(this).val()) || 0;
                const price = parseFloat(row.find('.item-price-hidden').val()) || 0;
                row.find('.item-total-price').val((qty * price).toFixed(2));
                calculateTotals();
            });

            // Recalculate when summary fields change
            $('#order_tax_select, #discountInput, #shippingInput').on('input change', calculateTotals);

            function calculateTotals() {
                // 1. Calculate SubTotal from all the item rows in the table
                let subTotal = 0;
                $('#quote-items-table tr.sale-item-row').each(function() {
                    // Find the "Sub Total" cell for this row and add its value
                    subTotal += parseFloat($(this).find('.item-total-price').val()) || 0;
                });

                // 2. Get values for tax, discount, and shipping from the form inputs
                const taxSelect = $('#order_tax_select');
                const orderTaxRate = parseFloat(taxSelect.find('option:selected').data('rate')) || 0;
                const discountType = $('#discount_type_hidden').val() || 'fixed';
                const discountValue = parseFloat($('#discount_value_hidden').val()) || 0;
                const shipping = parseFloat($('#shippingInput').val()) || 0;

                // 3. Calculate the final amounts
                let finalDiscountAmount = 0;
                if (discountType === 'percentage') {
                    finalDiscountAmount = subTotal * (discountValue / 100);
                } else { // 'fixed'
                    finalDiscountAmount = discountValue;
                }
                const orderTaxAmount = subTotal * (orderTaxRate / 100);
                const grandTotal = subTotal + orderTaxAmount - finalDiscountAmount + shipping;

                // --- THIS IS THE FIX ---
                // 4. Update ALL display elements, including the SubTotal
                $('#subTotalDisplay').text('$' + subTotal.toFixed(2)); // Updates the subtotal under the items table
                $('#grandTotalDisplay').text('$' + grandTotal.toFixed(
                    2)); // Updates the grand total in the summary card

                // Update the readonly discount amount input
                $('#discountInputAmount').val(finalDiscountAmount.toFixed(2));

                // Update the percentage display if needed
                if (discountType === 'percentage') {
                    $('#discount-percentage-display').text(`${discountValue}%`).show();
                } else {
                    $('#discount-percentage-display').hide();
                }
                // --- END OF FIX ---

                // 5. Update hidden inputs for the form submission
                $('#sub_total_hidden').val(subTotal.toFixed(2));
                $('#order_tax_amount_hidden').val(orderTaxAmount.toFixed(2));
                $('#discount_amount_hidden').val(finalDiscountAmount.toFixed(2));
                $('#grand_total_hidden').val(grandTotal.toFixed(2));
            }

            // --- LOGIC FOR THE DISCOUNT MODAL ---
            $('#discount-form').on('submit', function(e) {
                e.preventDefault();
                const type = $('#modal-discount-type').val();
                const value = $('#modal-discount-value').val();

                // Update the hidden inputs that drive the calculation
                $('#discount_type_hidden').val(type);
                $('#discount_value_hidden').val(value);

                calculateTotals(); // Recalculate everything

                bootstrap.Modal.getInstance(document.getElementById('discount')).hide();
            });

            // Sync modal with current values when it opens
            $('#discount').on('show.bs.modal', function() {
                // Populate the modal from the hidden inputs
                $('#modal-discount-type').val($('#discount_type_hidden').val());
                $('#modal-discount-value').val($('#discount_value_hidden').val());
            });

            // =========================================================================
            // INITIALIZATION
            // =========================================================================
            calculateTotals(); // Run once on page load to set the initial state correctly
        });
    </script>
@endpush
