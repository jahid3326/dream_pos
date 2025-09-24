@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="content">
            {{-- Make sure your form action and method are correct --}}
            <form action="{{ route('sales.update', $sale->id) }}" method="POST">
                @csrf
                @method('PUT')
                <h4>Edit Sale #{{ $sale->invoice_number }}</h4>

                {{-- Top Section: Invoice Number, Customer, Date --}}
                <div class="card">
                    <div class="card-body row">
                        <div class="col-md-4"><label>Invoice Number</label><input type="text" name="invoice_number"
                                class="form-control" value="{{ old('invoice_number', $sale->invoice_number) }}" required>
                        </div>
                        <div class="col-md-4"><label>Customer</label><select name="customer_id" id="customer_select"
                                class="form-select" required>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}" @selected(old('customer_id', $sale->customer_id) == $c->id)>{{ $c->user->name }}
                                    </option>
                                @endforeach
                            </select></div>
                        <div class="col-md-4"><label>Sales Date</label><input type="datetime-local" name="sales_date"
                                class="form-control"
                                value="{{ old('sales_date', $sale->sales_date->format('Y-m-d\TH:i')) }}" required></div>
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
                                        <th>Total Price</th>
                                        <th>Tax (%)</th>
                                        <th>Total HT</th>
                                        <th>Total TTC</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="sale-items-table">
                                    @php $currentIndex = 0; @endphp
                                    @foreach ($sale->categoryItems as $item)
                                        @include('sales._cart-item-row', [
                                            'item' => $item,
                                            'type' => 'category',
                                            'index' => $currentIndex++,
                                            'sale' => $sale,
                                        ])
                                    @endforeach
                                    @foreach ($sale->packItems as $item)
                                        @include('sales._cart-item-row', [
                                            'item' => $item,
                                            'type' => 'pack',
                                            'index' => $currentIndex++,
                                            'sale' => $sale,
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
                                    <select name="order_status" class="form-select">
                                        <option value="delivered" @selected(old('order_status', $sale->order_status) == 'delivered')>Delivered</option>
                                        <option value="on process" @selected(old('order_status', $sale->order_status) == 'on process')>On Process</option>
                                        <option value="in process" @selected(old('order_status', $sale->order_status) == 'in process')>In Process</option>
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
                                                    @selected(old('order_tax_id', $sale->order_tax_id) == $tax->id)>{{ $tax->name }} ({{ $tax->rate }}%)
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
                                            value="{{ number_format($sale->discount, 2, '.', '') }}" readonly>
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
                                        value="{{ old('shipping', $sale->shipping) }}">
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
                    value="{{ old('discount_type', $sale->discount_type ?? 'fixed') }}">
                <input type="hidden" name="discount_value" id="discount_value_hidden"
                    value="{{ old('discount_value', $sale->discount_rate ?? $sale->discount) }}">
                {{-- This hidden input stores the final calculated amount for the controller --}}
                <input type="hidden" name="discount_amount" id="discount_amount_hidden">
                <input type="hidden" name="grand_total" id="grand_total_hidden">

                <div class="text-end my-3">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary me-1">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update
                        Sale</button>
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
            let itemIndex = {{ $sale->categoryItems->count() + $sale->packItems->count() }};

            // Select2 Initialization for Products
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

            // Select2 Initialization for Packs
            $('#pack_option_search').select2({
                placeholder: 'Search by Pack, Surface, or Option...',
                minimumInputLength: 2,
                ajax: {
                    url: "{{ route('sales.pack-options.search') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });

            // --- EVENT LISTENERS ---

            // Add Category Item to Table
            $('#product_search').on('select2:select', function(e) {
                addItemRow(e.params.data, 'category');
                $(this).val(null).trigger('change');
            });

            // Add Pack Item to Table
            $('#pack_option_search').on('select2:select', function(e) {
                addItemRow(e.params.data, 'pack');
                $(this).val(null).trigger('change');
            });

            // Remove Item from Table
            $('#sale-items-table').on('click', '.remove-item-btn', function() {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            // Recalculate when quantity changes
            $('#sale-items-table').on('input', '.item-quantity', function() {
                updateRowCalculations($(this).closest('tr'));
                calculateTotals(); // Also update grand totals
            });

            // Recalculate when summary fields (tax, discount, shipping) change
            $('.calculation-trigger, #discount-form').on('input change submit', calculateTotals);


            // --- CORE FUNCTIONS ---

            /**
             * Adds a new item (product or pack) to the table.
             */
            function addItemRow(data, type) {
                const price = parseFloat(data.price) || 0;
                const name = (type === 'pack') ? data.name_for_cart : data.text;
                const id = data.id;

                // Prevent duplicates
                const alreadyExists = $(`#sale-items-table input[name$="[type]"][value="${type}"]`)
                    .siblings(`input[name$="[id]"][value="${id}"]`)
                    .length > 0;

                if (alreadyExists) {
                    Swal.fire('Already Added', 'This item is already in the list.', 'info');
                    return;
                }

                let hiddenFields = `
                    <input type="hidden" name="items[${itemIndex}][sale_item_id]" value="">
                    <input type="hidden" name="items[${itemIndex}][type]" value="${type}">
                    <input type="hidden" name="items[${itemIndex}][id]" value="${id}">
                    <input type="hidden" name="items[${itemIndex}][name]" value="${name}">
                    <input type="hidden" class="item-price-hidden" name="items[${itemIndex}][price]" value="${price}">
                `;

                if (type === 'category') {
                    hiddenFields +=
                        `<input type="hidden" name="items[${itemIndex}][variation_id]" value="${data.variation_id || ''}">`;
                }

                const newRowHtml = `
                    <tr class="sale-item-row">
                        ${hiddenFields}
                        <td>${itemIndex + 1}</td>
                        <td>${name}</td>
                        <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control item-quantity" value="1" min="1"></td>
                        <td class="text-end item-price-display">$0.00</td>
                        <td class="text-end item-total-price-display">$0.00</td>
                        <td class="text-end item-tax-display">0.00%</td>
                        <td class="text-end item-total-ht-display">$0.00</td>
                        <td class="text-end item-total-ttc-display fw-bold">$0.00</td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-item-btn">&times;</button></td>
                    </tr>`;

                const newRow = $(newRowHtml);
                $('#sale-items-table').append(newRow);
                itemIndex++;

                updateRowCalculations(newRow); // Calculate initial values for the new row
                calculateTotals(); // Update grand totals
            }

            /**
             * Updates the display values for a single row based on its quantity and the main order tax.
             */
            function updateRowCalculations(row) {
                const qty = parseFloat(row.find('.item-quantity').val()) || 0;
                const price = parseFloat(row.find('.item-price-hidden').val()) || 0;
                const totalPrice = qty * price;
                const totalHT = totalPrice;

                const orderTaxRate = parseFloat($('#order_tax_select option:selected').data('rate')) || 0;
                const totalTTC = totalHT * (1 + orderTaxRate / 100);

                // Update all display <td>s for THIS ROW
                row.find('.item-price-display').text('$' + price.toFixed(2));
                row.find('.item-total-price-display').text('$' + totalPrice.toFixed(2));
                row.find('.item-total-ht-display').text('$' + totalHT.toFixed(2));
                row.find('.item-tax-display').text(orderTaxRate.toFixed(0) + '%');
                row.find('.item-total-ttc-display').text('$' + totalTTC.toFixed(2));
            }

            /**
             * Calculates the SubTotal, Grand Total, and updates all summary fields.
             * Also ensures all rows have the correct tax rate displayed.
             */
            function calculateTotals() {
                // 1. Calculate SubTotal from the source-of-truth inputs
                let subTotal = 0;
                $('#sale-items-table tr.sale-item-row').each(function() {
                    const qty = parseFloat($(this).find('.item-quantity').val()) || 0;
                    const price = parseFloat($(this).find('.item-price-hidden').val()) || 0;
                    subTotal += qty * price;
                });

                // 2. As we're calculating totals, let's also ensure all rows are up-to-date with the current tax
                $('#sale-items-table tr.sale-item-row').each(function() {
                    updateRowCalculations($(this));
                });

                // 3. Get values for tax, discount, and shipping
                const taxSelect = $('#order_tax_select');
                const orderTaxRate = parseFloat(taxSelect.find('option:selected').data('rate')) || 0;
                const discountType = $('#discount_type_hidden').val() || 'fixed';
                const discountValue = parseFloat($('#discount_value_hidden').val()) || 0;
                const shipping = parseFloat($('#shippingInput').val()) || 0;

                // 4. Calculate final amounts
                let finalDiscountAmount = (discountType === 'percentage') ? subTotal * (discountValue / 100) :
                    discountValue;
                const orderTaxAmount = subTotal * (orderTaxRate / 100);
                const grandTotal = subTotal + orderTaxAmount - finalDiscountAmount + shipping;

                // 5. Update ALL display elements
                $('#subTotalDisplay').text('$' + subTotal.toFixed(2));
                $('#grandTotalDisplay').text('$' + grandTotal.toFixed(2));
                $('#discountInputAmount').val(finalDiscountAmount.toFixed(2));
                if (discountType === 'percentage') {
                    $('#discount-percentage-display').text(`${discountValue}%`).show();
                } else {
                    $('#discount-percentage-display').hide();
                }

                // 6. Update hidden inputs for form submission
                $('#sub_total_hidden').val(subTotal.toFixed(2));
                $('#order_tax_amount_hidden').val(orderTaxAmount.toFixed(2));
                $('#discount_amount_hidden').val(finalDiscountAmount.toFixed(2));
                $('#grand_total_hidden').val(grandTotal.toFixed(2));
            }

            // --- DISCOUNT MODAL LOGIC ---
            $('#discount-form').on('submit', function(e) {
                e.preventDefault();
                $('#discount_type_hidden').val($('#modal-discount-type').val());
                $('#discount_value_hidden').val($('#modal-discount-value').val());
                calculateTotals(); // Recalculate everything
                bootstrap.Modal.getInstance(document.getElementById('discount')).hide();
            });

            $('#discount').on('show.bs.modal', function() {
                $('#modal-discount-type').val($('#discount_type_hidden').val());
                $('#modal-discount-value').val($('#discount_value_hidden').val());
            });

            // =========================================================================
            // INITIALIZATION: Run once on page load to set the initial state correctly
            // =========================================================================
            calculateTotals();
        });
    </script>
@endpush
