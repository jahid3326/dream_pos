@extends('pos.layout')
@section('title', 'POS')
@section('content')
    <!-- Products -->
    <div class="col-md-12 col-lg-6">
        <div class="pos-categories tabs_wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <div>
                    <h5 class="mb-1">Welcome, {{ Auth::user()->name }}</h5>
                    <p>{{ now()->format('F d, Y') }}</p>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <div class="input-icon-start pos-search position-relative">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Search Product">
                    </div>
                    <a href="#" class="btn btn-sm btn-primary">View All Categories</a>
                </div>
            </div>

            {{-- Level 1: Main Horizontal Tabs (Packs + Parent Categories) --}}
            <div id="level-1-tabs" class="level">
                <ul class="tabs owl-carousel pos-carousel pos-category4 mb-0">
                    <li id="mode-packs" class="active" data-mode="packs">
                        <h6><a href="javascript:void(0);">Pack Meuble</a></h6>
                    </li>
                    @foreach ($parentCategories as $category)
                        <li id="cat-{{ $category->id }}" class="category-tab" data-mode="category"
                            data-id="{{ $category->id }}">
                            <h6><a href="javascript:void(0);">{{ $category->name }}</a></h6>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Level 2: Sub-Tabs (Pack Names or Child Categories) --}}
            <div id="level-2-tabs" class="level" style="display: none;"></div>

            {{-- Level 3: Sub-Tabs (Surfaces) --}}
            <div id="level-3-tabs" class="level" style="display: none;"></div>

            {{-- Level 4: Sub-Tabs (Options) --}}
            <div id="level-4-tabs" class="level" style="display: none;"></div>

            {{-- Product Grid --}}
            <div class="pos-products">
                <div id="product-grid-container">
                    {{-- Product cards will be rendered here by JavaScript --}}
                </div>
                <div class="mt-3 text-center">
                    <button class="btn btn-primary pack-actions-container" id="add-all-btn" style="display: none;">Add
                        All</button>
                    <button class="btn btn-primary category-actions-container" id="add-selection-btn"
                        style="display: none;">Add Selection</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Products -->
    <!-- Order Details -->
    <div class="col-md-12 col-lg-6 ps-0 theiaStickySidebar">
        <aside class="product-order-list">
            <div class="customer-info">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="d-flex align-items-center gap-2">
                            <div class="w-100">
                                {{-- Give the select an ID and a class for Select2 --}}
                                <select class="form-select" id="customer_select" name="customer_id">
                                    <option value="" selected>Walk-in Customer</option>
                                    @foreach ($customers as $customer)
                                        {{-- Only show the customer's name --}}
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- This button will now open our new modal --}}
                            <a href="#" class="btn btn-primary btn-icon" data-bs-toggle="modal"
                                data-bs-target="#addCustomerModal">
                                <i class="ti ti-user-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product-added block-section">
                <div class="product-wrap">
                    <div class="empty-cart text-center">
                        <div class="mb-1"><img src="{{ asset('public/assets/img/icons/empty-cart.svg') }}" alt="img">
                        </div>
                        <p class="fw-bold">No Products Selected</p>
                    </div>
                    <div class="table-responsive" style="display: none;">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th class="bg-transparent fw-bold">#</th>
                                    <th class="bg-transparent fw-bold">Product</th>
                                    <th class="bg-transparent fw-bold">QTY</th>
                                    <th class="bg-transparent fw-bold">Unit Price</th>
                                    <th class="bg-transparent fw-bold">Sub Total</th>
                                    <th class="bg-transparent fw-bold text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="pos-cart-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="block-section order-method bg-light m-0">
                <div class="order-total">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td>Sub Total</td>
                                    <td class="text-end" id="cart-subtotal">$0.00</td>
                                </tr>
                                <tr>
                                    <td>
                                        Shipping
                                        {{-- This link can open a modal to set the shipping cost --}}
                                        <a href="#" class="ms-3 link-default" data-bs-toggle="modal"
                                            data-bs-target="#shipping-cost">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        {{-- Hidden input to store the shipping value --}}
                                        <input type="hidden" id="shipping-value" value="0">
                                    </td>
                                    <td class="text-end" id="cart-shipping">$0.00</td>
                                </tr>
                                <tr>
                                    <td>
                                        Tax (<span id="tax-name-display">None</span>)
                                        <a href="#" class="ms-3 link-default" data-bs-toggle="modal"
                                            data-bs-target="#order-tax">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        {{-- Hidden input to store the selected tax rate for calculations --}}
                                        <input type="hidden" id="order-tax-rate" value="0">
                                    </td>
                                    <td class="text-end" id="cart-tax">$0.00</td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-danger">Discount</span>
                                        <a href="#" class="ms-3 link-default" data-bs-toggle="modal"
                                            data-bs-target="#discount">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <input type="hidden" id="discount-value" value="0">
                                    </td>
                                    <td class="text-danger text-end" id="cart-discount">-$0.00</td>
                                </tr>
                                <tr class="total-row">
                                    <td class="fw-bold">Grand Total</td>
                                    <td class="text-end fw-bold" id="cart-grandtotal">$0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row gx-2">
                    <div class="col-sm-3">
                        <button class="btn btn-info d-flex align-items-center justify-content-center w-100 mb-2"
                            id="generate-quote-btn">
                            <i class="ti ti-receipt me-2"></i>Generate quote</a>
                        </button>
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-info d-flex align-items-center justify-content-center w-100 mb-2"
                            id="generate-invoice-btn">
                            <i class="ti ti-receipt me-2"></i>Generate Invoice</a>
                        </button>
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-success d-flex align-items-center justify-content-center w-100 mb-2"
                            id="pay-now-btn">
                            <i class="ti ti-cash-banknote me-2"></i>Pay now</a>
                        </button>
                    </div>
                    <div class="col-sm-3">
                        <button
                            class="btn btn-outline-secondary d-flex align-items-center justify-content-center w-100 mb-2"
                            id="reset-btn">
                            <i class="ti ti-reload me-2"></i>Reset</a>
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    </div>
    <!-- /Order Details -->
    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addCustomerForm">
                    @csrf
                    <div class="modal-body">
                        <div id="customer-errors" class="alert alert-danger" style="display: none;"></div>
                        <div class="row">
                            {{-- User Information --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" name="profile_picture" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <hr class="my-3">

                            {{-- Customer-Specific Information --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tax Number</label>
                                <input type="text" name="tax_number" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="1" selected>Enabled</option>
                                    <option value="0">Disabled</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Billing Address</label>
                                <textarea name="billing_address" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary me-1" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Finalize Payment & Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="payment-form">
                    <div class="modal-body">
                        <div id="payment-errors" class="alert alert-danger" style="display: none;"></div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Date</label>
                                <input type="date" name="payment_date" id="payment-date" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Mode<span class="text-danger">*</span></label>
                                <select name="payment_mode" class="form-select" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Amount<span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" id="payment-amount"
                                    class="form-control" required>
                                <small class="form-text text-muted">Max. Amount: <span id="max-payable-text"
                                        class="fw-bold"></span></small>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Payment Note</label>
                                <textarea name="payment_note" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary me-1" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Payment & Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {

            // =========================================================================
            // STATE & CACHE
            // =========================================================================
            const ORDER_STORAGE_KEY = 'pos_order';

            localStorage.removeItem(ORDER_STORAGE_KEY);

            let order = {}; // The single source of truth for the current order state

            // Convert Blade data to JS variables for high-performance client-side operations
            const packsData = {!! json_encode($packs) !!};
            const categoriesData = {!! json_encode($parentCategories) !!};

            // Cache jQuery selectors for performance
            const productGridContainer = $('#product-grid-container');
            const level1Container = $('#level-1-tabs');
            const level2Container = $('#level-2-tabs');
            const level3Container = $('#level-3-tabs');
            const level4Container = $('#level-4-tabs');
            const packActionsContainer = $('.pack-actions-container');
            const categoryActionsContainer = $('.category-actions-container');
            const cartBody = $('#pos-cart-body');
            const emptyCartMessage = $('.empty-cart');
            const cartTableContainer = cartBody.closest('.table-responsive');


            // Initialize Bootstrap 5 modals
            const taxModal = new bootstrap.Modal(document.getElementById('order-tax'));
            const shippingModal = new bootstrap.Modal(document.getElementById('shipping-cost'));
            const discountModal = new bootstrap.Modal(document.getElementById('discount'));

            // Create a hidden input for discount type if it doesn't exist
            if (!$('#discount-type-hidden').length) {
                $('body').append('<input type="hidden" id="discount-type-hidden" value="fixed">');
            }

            // =========================================================================
            // HELPER FUNCTIONS (localStorage)
            // =========================================================================
            function loadOrderFromStorage() {
                const storedOrder = localStorage.getItem(ORDER_STORAGE_KEY);
                order = storedOrder ? JSON.parse(storedOrder) : {
                    items: {},
                    shipping: 0,
                    discount: 0,
                    discount_type: 'fixed',
                    tax_rate: 0,
                    tax_name: 'None'
                };
            }

            function saveOrderToStorage() {
                localStorage.setItem(ORDER_STORAGE_KEY, JSON.stringify(order));
            }

            function clearOrder() {
                order = {
                    items: {},
                    shipping: 0,
                    discount: 0,
                    discount_type: 'fixed',
                    tax_rate: 0,
                    tax_name: 'None'
                };
                saveOrderToStorage();
                renderCart();
            }

            // =========================================================================
            // CORE UI & CALCULATION FUNCTIONS
            // =========================================================================


            /** Renders a set of filter tabs and initializes it as an Owl Carousel. */
            function renderTabsAsCarousel(container, items, className, dataKey, nameKey = 'name') {
                container.empty().show();
                let tabsHtml = '<ul class="tabs owl-carousel pos-carousel pos-category4 mb-0">';
                items.forEach(item => {
                    const priceAttribute = item.price ? `data-price="${item.price}"` : '';
                    tabsHtml +=
                        `<li id="${dataKey}-${item.id}" class="${className}" data-${dataKey}-id="${item.id}" data-id="${item.id}" data-name="${item[nameKey]}" ${priceAttribute}><h6><a href="javascript:void(0);">${item[nameKey]}</a></h6></li>`;
                });
                tabsHtml += '</ul>';
                container.html(tabsHtml);

                container.find('.owl-carousel').owlCarousel({
                    loop: false,
                    margin: 15,
                    nav: true,
                    dots: false,
                    autoWidth: true,
                    responsive: {
                        0: {
                            items: 3
                        },
                        600: {
                            items: 5
                        },
                        1000: {
                            items: 7
                        }
                    }
                });
            }

            /** Renders a grid of product cards. */
            function renderProductGrid(productsToRender) {
                productGridContainer.empty().html('<div class="row g-3"></div>');
                const productRow = productGridContainer.find('.row');

                if (!productsToRender || productsToRender.length === 0) {
                    productRow.html(
                        '<div class="col-12"><p class="text-center text-muted p-4">No products are assigned to this category.</p></div>'
                    );
                    return;
                }

                productsToRender.forEach(product => {
                    const productHtml = `
                    <div class="col-sm-6 col-md-4 col-lg-4 product-item">
                        <div class="card h-100 product-info product-info-category" 
                            data-product-id="${product.id}" data-variation-id="${product.variation_id || ''}"
                            data-name="${product.name}" data-price="${product.price}">
                            <div class="form-check product-checkbox-container mx-2">
                                <input class="form-check-input product-select-check" type="checkbox">
                            </div>
                            <div class="card-body p-2 d-flex align-items-center justify-content-center">
                                <a href="javascript:void(0);" class="product-image">
                                    <img src="${product.image}" alt="Product">
                                </a>
                            </div>
                            <div class="card-footer text-center">
                                <h6 class="fs-14 fw-bold mb-1">${product.name}</h6>
                                <small class="text-muted">${product.measurement || ''}</small>
                            </div>
                        </div>
                    </div>`;
                    productRow.append(productHtml);
                });
                syncActiveProductCards();
            }

            // --- CLICK LISTENER FOR CATEGORY MODE (WITH Checkboxes) ---
            productGridContainer.on('click', '.product-info-category', function(event) {
                const card = $(this);
                const checkbox = card.find('.product-select-check');
                if (!$(event.target).is('.product-select-check')) {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                }
                card.toggleClass('selected', checkbox.prop('checked')); // Use a 'selected' class
            });

            // --- "ADD SELECTION" BUTTON (for Category Mode) ---
            /*
            categoryActionsContainer.on('click', function() {

                const selectedProducts = [];
                productGridContainer.find('.product-select-check:checked').each(function() {
                    const card = $(this).closest('.product-info-category');
                    selectedProducts.push(card.data());
                });
                console.log("Selected Category Products:", selectedProducts);
                // alert(`${selectedProducts.length} item(s) selected. See console.`);
                // Here you would loop through selectedProducts and add them to the cart
            });
            */

            categoryActionsContainer.on('click', function() {
                const selectedCards = productGridContainer.find('.product-select-check:checked');

                if (selectedCards.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No selected products to Add',
                        text: 'Please select at least one product to add.',
                    });
                    return;
                }

                // 1. Loop through the selected products and add them to the cart object.
                selectedCards.each(function() {
                    const card = $(this).closest('.product-info-category');
                    const productData = card.data();

                    // Use 'c' prefix for Category items to distinguish from Pack items if needed
                    const cartId = `c-${productData.productId}-${productData.variationId || '0'}`;

                    // Get the category name from the active tab for the description
                    const categoryName = $('#level-2-tabs').find('.active h6 a').text() ||
                        'Category';

                    // Add to the cart object, or update quantity if it already exists
                    if (order.items[cartId]) {
                        order.items[cartId].quantity++;
                    } else {
                        order.items[cartId] = {
                            type: 'category',
                            id: productData.productId,
                            variation_id: productData.variationId,
                            name: productData.name,
                            price: parseFloat(productData.price),
                            quantity: 1,
                            category: categoryName // This is the descriptive text
                        };
                    }
                });

                // 2. Persist the updated cart to localStorage.
                saveOrderToStorage();

                // 3. Redraw the entire UI to reflect the changes.
                renderCart();

                // 4. Provide feedback to the user.
                console.log(`${selectedCards.length} item(s) added to the cart.`);

                // 5. Uncheck the boxes after adding them.
                selectedCards.prop('checked', false);
                $('.product-info-category').removeClass('selected');
            });

            /** Renders the entire cart UI. */
            function renderCart() {
                cartBody.empty();
                let subTotal = 0;
                let totalItems = 0;

                // 2. Check if the cart is empty to show/hide the placeholder message
                if (Object.keys(order.items).length > 0) {
                    emptyCartMessage.hide();
                    cartTableContainer.show();
                } else {
                    emptyCartMessage.show();
                    cartTableContainer.hide();
                }

                // 3. Loop through each item in the cart object
                let itemCounter = 0;
                for (const cartId in order.items) {
                    itemCounter++;
                    const item = order.items[cartId];

                    // 3a. Calculate totals for this item
                    const itemSubTotal = item.price * item.quantity;
                    subTotal += itemSubTotal;
                    totalItems += item.quantity;

                    // 3b. Determine the display name based on the item type
                    let displayNameHtml = '';
                    if (item.type === 'pack') {
                        // For pack items: Pack Name | Option | Surface
                        displayNameHtml = `<strong>${item.name}</strong>`;
                    } else { // 'category'
                        // For category items: Product Name | Category
                        displayNameHtml =
                            `<strong>${item.name}</strong><br><small class="text-muted">${item.category}</small>`;
                    }

                    // 3c. Create the HTML row for the cart table
                    const rowHtml = `
            <tr data-cart-id="${cartId}">
                <td>${itemCounter}</td>
                <td>${displayNameHtml}</td>
                <td><div class="qty-item m-0"><input type="number" class="form-control text-center cart-quantity" value="${item.quantity}" min="1"></div></td>
                <td>$${item.price.toFixed(2)}</td>
                <td>$${itemSubTotal.toFixed(2)}</td>
                <td class="text-end"><a class="btn-icon delete-icon remove-item-btn" href="javascript:void(0);"><i class="ti ti-trash"></i></a></td>
            </tr>`;

                    // 3d. Append the new row to the table
                    cartBody.append(rowHtml);
                }

                // 4. After rendering the cart, sync the visual state of product cards
                syncActiveProductCards();

                // 5. Finally, update all the summary totals
                calculateTotals();
            }

            function calculateTotals() {
                // 1. Calculate SubTotal by iterating through the items in the global 'order' object.
                let subTotal = 0;
                for (const cartId in order.items) {
                    subTotal += order.items[cartId].price * order.items[cartId].quantity;
                }

                // 2. Get additional cost values directly from the 'order' state object.
                const shipping = parseFloat(order.shipping) || 0;
                const discountValue = parseFloat(order.discount) || 0;
                const discountType = order.discount_type || 'fixed';
                const orderTaxRate = parseFloat(order.tax_rate) || 0;

                // 3. Calculate the discount amount based on its type.
                let discountAmount = 0;
                if (discountType === 'percentage') {
                    discountAmount = subTotal * (discountValue / 100);
                } else { // 'fixed'
                    discountAmount = discountValue;
                }

                // 4. Calculate the order tax amount based on the subtotal.
                const orderTaxAmount = subTotal * (orderTaxRate / 100);

                // 5. Calculate the final Grand Total.
                const grandTotal = subTotal + orderTaxAmount + shipping - discountAmount;

                // 6. Update all display elements in the summary table.
                $('#cart-subtotal').text(`$${subTotal.toFixed(2)}`);
                $('#cart-shipping').text(`$${shipping.toFixed(2)}`);
                $('#cart-tax').text(`$${orderTaxAmount.toFixed(2)}`);
                $('#tax-name-display').text(order.tax_name || 'None');
                $('#cart-discount').text(`-$${discountAmount.toFixed(2)}`);
                $('#cart-grandtotal').text(`$${grandTotal.toFixed(2)}`);

                // 7. Update other UI elements like the item count and 'Pay' button.
                const totalItems = Object.values(order.items).reduce((sum, item) => sum + item.quantity, 0);
                $('.badge:contains("Items") .text-teal').text(totalItems);

                // 8. Update hidden inputs for form submission.
                $('#sub_total_hidden').val(subTotal.toFixed(2));
                $('#order_tax_amount_hidden').val(orderTaxAmount.toFixed(2));
                $('#grand_total_hidden').val(grandTotal.toFixed(2));
            }

            function syncActiveProductCards() {

                $('.product-info-category').removeClass('active');
                for (const cartId in order.items) {
                    const item = order.items[cartId];
                    if (item.type == 'category') {
                        console.log(item);
                        $(`.product-info-category[data-product-id="${item.id}"][data-variation-id="${item.variation_id || ''}"]`)
                            .addClass('active');
                    }
                }

            }

            /** Syncs checkboxes on product cards with the current cart state (for Pack Mode). */
            function syncPackProductCheckboxes() {
                $('.product-select-check').prop('checked', false);
                for (const cartId in order.items) {
                    const item = order.items[cartId];
                    $(`.product-info-category[data-product-id="${item.id}"][data-variation-id="${item.variation_id || ''}"]`)
                        .find('.product-select-check').prop('checked', true);
                }
            }

            // =========================================================================
            // EVENT LISTENERS
            // =========================================================================

            // --- LEVEL 1: Main Mode Switch ---
            level1Container.on('click', 'li', function() {
                level1Container.find('li').removeClass('active');
                $(this).addClass('active');
                level2Container.empty().hide();
                level3Container.empty().hide();
                level4Container.empty().hide();
                productGridContainer.empty();
                packActionsContainer.hide();
                categoryActionsContainer.hide();

                const mode = $(this).data('mode');
                if (mode === 'packs') {
                    renderTabsAsCarousel(level2Container, packsData, 'pack-name-tab', 'pack');
                } else { // mode === 'category'
                    const categoryId = $(this).data('id');
                    const category = categoriesData.find(c => c.id == categoryId);
                    if (category && category.children) {
                        renderTabsAsCarousel(level2Container, category.children, 'child-category-tab',
                            'cat');
                    }
                }
            });

            // --- LEVEL 2: Click Pack Name or Child Category ---
            level2Container.on('click', 'li', function() {
                level2Container.find('li').removeClass('active');
                $(this).addClass('active');
                level3Container.empty().hide();
                level4Container.empty().hide();
                productGridContainer.empty();
                packActionsContainer.hide();
                categoryActionsContainer.hide();

                if ($(this).hasClass('pack-name-tab')) {
                    const packId = $(this).data('pack-id');
                    const pack = packsData.find(p => p.id == packId);
                    if (pack && pack.groups) {
                        renderTabsAsCarousel(level3Container, pack.groups, 'surface-tab', 'group',
                            'surface');
                    }
                } else if ($(this).hasClass('child-category-tab')) {
                    const categoryId = $(this).data('cat-id');
                    productGridContainer.html('<p class="text-center p-4">Loading...</p>');
                    // const productsToShow = productsData.filter(p => p.category_id == categoryId);
                    // renderProductGrid(productsToShow, false);
                    $.ajax({
                        url: `{{ url('category-products') }}/${categoryId}/products`,
                        type: 'GET',
                        success: function(response) {
                            // console.log(response);
                            // Render the grid with the products returned from the server
                            renderProductGrid(response, false); // false = not in pack mode
                            categoryActionsContainer.show();
                        },
                        error: function() {
                            productGridContainer.html(
                                '<p class="text-center text-danger p-4">Could not load products.</p>'
                            );
                        }
                    });
                }
            });

            // --- LEVEL 3: Click Surface ---
            level3Container.on('click', 'li.surface-tab', function() {
                // 1. Set the active class on the clicked <li>
                level3Container.find('li').removeClass('active');
                $(this).addClass('active');

                // 2. Reset all lower levels
                level4Container.empty().hide();
                productGridContainer.empty();
                packActionsContainer.hide();
                categoryActionsContainer.hide();

                // 3. Find the currently active 'pack name' tab from the level above
                const activePackTab = level2Container.find('li.active');
                if (!activePackTab.length) {
                    console.error("Error: Could not find the active pack name in Level 2.");
                    return; // Stop if the parent pack can't be found
                }

                // 4. Get the packId from the correct element
                const packId = activePackTab.data('pack-id');
                const pack = packsData.find(p => p.id == packId);

                // 5. Get the groupId from the <li> that was just clicked
                const groupId = $(this).data('group-id');
                const group = pack ? pack.groups.find(g => g.id == groupId) : null;

                // 6. If the group and its options exist, render the next level of tabs
                if (group && group.options) {
                    // Give each option a user-friendly name like "Option 1"
                    group.options.forEach(opt => opt.name = `Option ${opt.option}`);
                    renderTabsAsCarousel(level4Container, group.options, 'option-tab', 'option');
                }
            });

            // --- LEVEL 4: Click Pack Option ---
            level4Container.on('click', 'li.option-tab', function() {
                level4Container.find('li').removeClass('active');
                $(this).addClass('active');
                const optionId = $(this).data('option-id');

                productGridContainer.html('<p class="text-center p-4">Loading...</p>');
                packActionsContainer.hide();
                categoryActionsContainer.hide();

                if (optionId) {
                    $.ajax({
                        url: `{{ url('pack-options') }}/${optionId}/products`,
                        type: 'GET',
                        success: function(response) {
                            // console.log(response)
                            renderPackProductGrid(response);
                            packActionsContainer.show();
                        },
                        error: () => productGridContainer.html(
                            '<p class="text-center text-danger p-4">Could not load products.</p>'
                        )
                    });
                }
            });

            /** 
             * Renders a grid of product cards, with sliders for variation products.
             * This is the definitive function for your design.
             */

            function renderPackProductGrid(parentProducts) {
                productGridContainer.empty().html('<div class="row g-3 product-grid-row"></div>');
                const productRow = productGridContainer.find('.row');

                if (!parentProducts || parentProducts.length === 0) {
                    productRow.html(
                        '<div class="col-12"><p class="text-center text-muted p-4">No products are assigned to this pack option.</p></div>'
                    );
                    return;
                }

                parentProducts.forEach(product => {
                    let finalHtml = '';

                    // --- LOGIC TO DECIDE: RENDER A SINGLE CARD OR A CAROUSEL OF CARDS ---

                    // Case 1: It's a VARIATION product with selected variations
                    if (product.type === 'variation' && product.pivot.selected_variations && product.pivot
                        .selected_variations.length > 0) {

                        // Start the carousel wrapper for this product
                        let slidesHtml = '<div class="owl-carousel variation-product-slider">';

                        // Loop through each variation and create a FULL CARD for each one
                        product.pivot.selected_variations.forEach(item => {
                            if (item.variation) {
                                const variation = item.variation;
                                const imageUrl = variation.image ?
                                    `{{ asset('public/storage') }}/${variation.image}` :
                                    `{{ asset('public/storage/images/default_image.png') }}`;

                                slidesHtml += `
                                <div class="item">
                                    <div class="card h-100 product-info product-info-pack" 
                                        data-product-id="${variation.product_id}" data-variation-id="${variation.id}"
                                        data-name="${variation.product.name} - ${variation.measurement}" data-price="${variation.sale_price}">
                                        <div class="card-body p-2 d-flex align-items-center justify-content-center">
                                            <a href="javascript:void(0);" class="product-image">
                                                <img src="${imageUrl}" alt="Variation">
                                            </a>
                                        </div>
                                        <div class="card-footer text-center">
                                            <h6 class="fs-14 fw-bold mb-1">${variation.product.name}</h6>
                                            <small class="text-muted">${variation.measurement}</small>
                                        </div>
                                    </div>
                                </div>`;
                            } else {
                                const product = item.product;
                                const productImageUrl = product.product_image ?
                                    `{{ asset('public/storage') }}/${product.product_image}` :
                                    `{{ asset('public/storage/images/default_image.png') }}`;

                                slidesHtml += `
                                <div class="item">
                                    <div class="card h-100 product-info product-info-pack" 
                                        data-product-id="${product.id}" data-variation-id=""
                                        data-name="${product.name} - ${product.measurement}" data-price="${product.sale_price}">
                                        <div class="card-body p-2 d-flex align-items-center justify-content-center">
                                            <a href="javascript:void(0);" class="product-image">
                                                <img src="${productImageUrl}" alt="Product">
                                            </a>
                                        </div>
                                        <div class="card-footer text-center">
                                            <h6 class="fs-14 fw-bold mb-1">${product.name}</h6>
                                            <small class="text-muted">${product.measurement}</small>
                                        </div>
                                    </div>
                                </div>`;
                            }
                        });
                        slidesHtml += '</div>';

                        // The entire column contains the carousel
                        finalHtml =
                            `<div class="col-sm-6 col-md-4 col-lg-4 product-item">${slidesHtml}</div>`;
                    }
                    // Case 2: It's a SINGLE product
                    else if (product.type === 'single') {
                        const imageUrl = product.product_image ?
                            `{{ asset('public/storage') }}/${product.product_image}` :
                            `{{ asset('public/storage/images/default_image.png') }}`;
                        finalHtml = `
                        <div class="col-sm-6 col-md-4 col-lg-4 item">
                            <div class="card h-100 product-info product-info-pack" 
                                data-product-id="${product.id}" data-variation-id=""
                                data-name="${product.name}" data-price="${product.sale_price}">
                                <div class="card-body p-2 d-flex align-items-center justify-content-center">
                                    <a href="javascript:void(0);" class="product-image">
                                        <img src="${imageUrl}" alt="Product">
                                    </a>
                                </div>
                                <div class="card-footer text-center">
                                    <h6 class="fs-14 fw-bold mb-1">${product.name}</h6>
                                    <small class="text-muted">${product.measurement || ''}</small>
                                </div>
                            </div>
                        </div>`;
                    }

                    productRow.append(finalHtml);
                });

                // Initialize all the new carousels
                $('.variation-product-slider').owlCarousel({
                    items: 1, // Show one full card at a time
                    loop: false,
                    margin: 10,
                    nav: true, // Show next/previous buttons
                    dots: false
                });

                // syncPackProductCheckboxes();
            }

            // --- "ADD ALL" BUTTON (for Pack Mode) ---
            packActionsContainer.on('click', function() {
                addSelectedPackItemsToCart()
            });

            function addSelectedPackItemsToCart() {

                const productItems = productGridContainer.find('.product-item');

                if (productItems.length === 0) {
                    // If the grid is empty, show an alert and stop.
                    Swal.fire({
                        icon: 'info',
                        title: 'No Products to Add',
                        text: 'This pack option appears to be empty.',
                    });
                    return; // Exit the function
                }

                const activePackTab = level2Container.find('li.active');
                const activeSurfaceTab = level3Container.find('li.active');
                const activeOptionTab = level4Container.find('li.active');

                // Read the price directly from the active option tab's data attribute
                const optionPrice = parseFloat(activeOptionTab.data('price')) || 0;

                const packContext = {
                    pack_id: activePackTab.data('id'),
                    pack_name: activePackTab.data('name'),
                    group_id: activeSurfaceTab.data('id'),
                    group_name: activeSurfaceTab.data('name'),
                    option_id: activeOptionTab.data('id'),
                    option_name: activeOptionTab.data('name'),
                    option_price: optionPrice // Use the price we just read
                };

                const cartItemName =
                    `${packContext.pack_name} | ${packContext.option_name} | ${packContext.group_name}`;
                const cartId = `p-${packContext.option_id}`;

                if (order.items[cartId]) {
                    order.items[cartId].quantity++;
                } else {
                    order.items[cartId] = {
                        type: 'pack',
                        id: packContext.option_id,
                        name: cartItemName,
                        price: packContext.option_price,
                        quantity: 1,
                        category: 'Pack',
                    };
                }

                saveOrderToStorage();
                renderCart();

                // console.log("Added Pack Item to Cart:", order.items[cartId]);
                // alert('Pack option added to cart!');
            }

            // --- CART ITEM INTERACTIONS ---
            cartBody.on('input', '.cart-quantity', function() {
                const cartId = $(this).closest('tr').data('cart-id');
                const newQuantity = parseInt($(this).val());
                if (order.items[cartId]) {
                    if (newQuantity > 0) {
                        order.items[cartId].quantity = newQuantity;
                    } else {
                        delete order.items[cartId];
                    }
                    saveOrderToStorage();
                    renderCart();
                }
            });
            cartBody.on('click', '.remove-item-btn', function() {
                // 1. Get the unique ID of the cart item from the parent <tr>'s data attribute.
                const cartId = $(this).closest('tr').data('cart-id');

                // 2. Check if the item exists in our cart object and delete it.
                if (order.items[cartId]) {

                    Swal.fire({
                        title: "Are you sure?",
                        text: "You want to delete the item?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, delete it!", // ✅ This works
                        cancelButtonText: "Cancel",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-danger ml-1"
                        },
                        buttonsStyling: false
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            delete order.items[cartId];
                            saveOrderToStorage();
                            renderCart();
                        }
                    });
                }
            });

            // --- MODAL LOGIC ---
            $('#order-tax-form').on('submit', function(e) {
                e.preventDefault();
                const selectedOption = $('#modal-tax-select').find('option:selected');
                order.tax_rate = parseFloat(selectedOption.data('rate')) || 0;
                order.tax_name = selectedOption.data('name') || 'None';
                saveOrderToStorage();
                calculateTotals();
                taxModal.hide();
            });
            $('#shipping-cost-form').on('submit', function(e) {
                e.preventDefault();
                const shippingCost = parseFloat($('#modal-shipping-input').val()) || 0;

                // Update the order object
                order.shipping = shippingCost;
                // Update the hidden input (for consistency, though calculations use the order object)
                $('#shipping-value').val(shippingCost);

                saveOrderToStorage();
                calculateTotals();
                shippingModal.hide();
            });
            $('#discount-form').on('submit', function(e) {
                e.preventDefault();
                const discountType = $('#modal-discount-type').val();
                const discountValue = parseFloat($('#modal-discount-value').val()) || 0;

                // Update the order object
                order.discount_type = discountType;
                order.discount = discountValue;
                // Update hidden inputs
                $('#discount-type-hidden').val(discountType);
                $('#discount-value').val(discountValue);

                saveOrderToStorage();
                calculateTotals();
                discountModal.hide();
            });

            // --- SYNC MODALS ON OPEN ---
            $('#order-tax').on('show.bs.modal', () => {
                let tr = parseFloat(order.tax_rate);
                $('#modal-tax-select').find(
                    `option[data-rate="${tr.toFixed(2)}"]`).prop('selected', true)
            });

            $('#shipping-cost').on('show.bs.modal', function() {
                $('#modal-shipping-input').val(order.shipping);
            });

            $('#discount').on('show.bs.modal', () => {
                $('#modal-discount-type').val(order.discount_type);
                $('#modal-discount-value').val(order.discount);
            });

            // =========================================================================
            // INITIALIZATION
            // =========================================================================
            loadOrderFromStorage();
            renderCart();

            // Initialize the main carousel that's already in the HTML
            $('#level-1-tabs .owl-carousel').owlCarousel({
                loop: false,
                margin: 15,
                nav: true,
                dots: false,
                autoWidth: true,
                responsive: {
                    0: {
                        items: 3
                    },
                    600: {
                        items: 5
                    },
                    1000: {
                        items: 7
                    }
                }
            });

            // Start in "Pack Meuble" mode by default
            $('#mode-packs').click();

            const customerSelect = $('#customer_select');
            customerSelect.select2({
                placeholder: 'Search for a customer...',
                width: '100%'
            });

            // 2. Initialize the modal instance
            const addCustomerModal = new bootstrap.Modal(document.getElementById('addCustomerModal'));
            const customerForm = $('#addCustomerForm');

            // 3. Handle the AJAX form submission for the new customer modal
            customerForm.on('submit', function(e) {
                e.preventDefault(); // Prevent the default page reload
                const form = $(this);
                const url = "{{ route('customers.ajaxStore') }}";
                const submitButton = form.find('button[type="submit"]');
                const originalButtonText = submitButton.html();

                // Use FormData to correctly handle file uploads
                const formData = new FormData(this);

                // UI feedback
                submitButton.prop('disabled', true).html('Saving...');
                $('#customer-errors').hide().html('');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData, // Send the FormData object
                    processData: false, // Essential for file uploads
                    contentType: false, // Essential for file uploads
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const newCustomer = response.customer;
                            const user = newCustomer.user;

                            // Create a new <option> element
                            const newOption = new Option(
                                user.name, // Display Text (Only the name)
                                newCustomer.id, // Value
                                true, // Default Selected
                                true // Is Selected
                            );

                            // Add the new option to the Select2 dropdown
                            customerSelect.append(newOption).trigger('change');

                            addCustomerModal.hide();
                            form[0].reset();
                        }
                    },
                    error: function(xhr) {
                        // Handle validation and other server errors
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            const errorContainer = $('#customer-errors');
                            let errorHtml = '<ul>';
                            errors.forEach(error => errorHtml += '<li>' + error + '</li>');
                            errorHtml += '</ul>';
                            errorContainer.html(errorHtml).show();
                        } else {
                            alert(
                                'An unexpected server error occurred. Please try again later.'
                            );
                        }
                    },
                    complete: function() {
                        // Always re-enable the button
                        submitButton.prop('disabled', false).html(originalButtonText);
                    }
                });
            });


            // =========================================================================
            // FOOTER ACTION BUTTONS & SALE SUBMISSION (FRESH CODE)
            // =========================================================================
            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));

            /** Universal helper function to gather all data for submission from the current POS state. */
            function gatherSaleData() {
                calculateTotals();
                const subTotal = calculateSubTotal();
                let discountAmount = (order.discount_type === 'percentage') ?
                    subTotal * (order.discount / 100) :
                    order.discount;
                return {
                    _token: '{{ csrf_token() }}',
                    customer_id: $('#customer_select').val(),
                    order_status: 'on process',
                    items: Object.values(order.items),
                    sub_total: calculateSubTotal(),
                    shipping: order.shipping,
                    discount_amount: discountAmount, // The final calculated amount in $
                    discount_type: order.discount_type, // 'fixed' or 'percentage'
                    discount_value: order.discount, // The raw value (e.g., 10 or 10.00)
                    order_tax_id: findTaxIdByRate(order.tax_rate),
                    order_tax_amount: calculateOrderTaxAmount(calculateSubTotal(), order.tax_rate),
                    grand_total: calculateGrandTotal(),
                };
            }

            /** Universal helper to handle the AJAX call and server response. */
            function submitSale(url, data, button) {
                const originalButtonText = button.html();
                $('#generate-invoice-btn, #pay-now-btn').prop('disabled', true);
                button.html('<span class="spinner-border spinner-border-sm"></span> Processing...');

                $.ajax({
                    url: url,
                    type: "POST",
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                        /*
                        if (response.success) {
                            Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 2000
                                })
                                .then(() => {
                                    clearOrder();
                                    window.location.href = response.redirect_url;
                                });
                        }
                        */
                    },
                    error: function(xhr) {
                        let errorMessage = 'An unexpected server error occurred.';
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Submission Failed',
                            html: errorMessage
                        });
                    },
                    complete: function() {
                        $('#generate-invoice-btn, #pay-now-btn').prop('disabled', false);
                        button.html(originalButtonText);
                    }
                });
            }

            // --- "GENERATE INVOICE" BUTTON (No Payment) ---
            $('#generate-invoice-btn').on('click', function(e) {
                e.preventDefault();
                if (Object.keys(order.items).length === 0) {
                    Swal.fire('Validation Error', 'Please add items.', 'warning');
                    return;
                }
                if (!$('#customer_select').val()) {
                    Swal.fire('Validation Error', 'Please select a customer.', 'warning');
                    return;
                }
                const saleData = gatherSaleData();
                console.log(saleData);
                submitSale("{{ route('sales.store.invoice') }}", saleData, $(this));
            });

            // --- "PAY NOW" BUTTON (Opens Modal) ---
            $('#pay-now-btn').on('click', function(e) {
                e.preventDefault();
                if (Object.keys(order.items).length === 0) {
                    Swal.fire('Validation Error', 'Please add items.', 'warning');
                    return;
                }
                if (!$('#customer_select').val()) {
                    Swal.fire('Validation Error', 'Please select a customer.', 'warning');
                    return;
                }

                const grandTotal = calculateGrandTotal();
                const today = new Date().toISOString().split('T')[0];

                $('#payment-date').val(today);
                $('#max-payable-text').text(`$${grandTotal.toFixed(2)}`);
                $('#payment-amount').attr('max', grandTotal.toFixed(2)).val(grandTotal.toFixed(2));

                $('#payment-errors').hide().html('');
                paymentModal.show();
            });

            // --- PAYMENT MODAL SUBMIT (Invoice WITH Payment) ---
            $('#payment-form').on('submit', function(e) {
                e.preventDefault();
                const paymentForm = $(this);
                const saleData = gatherSaleData();

                saleData.payment_mode = paymentForm.find('[name="payment_mode"]').val();
                saleData.payment_date = paymentForm.find('[name="payment_date"]').val();
                saleData.payment_note = paymentForm.find('[name="payment_note"]').val();
                saleData.amount = parseFloat(paymentForm.find('[name="amount"]').val());
                saleData.order_status = 'delivered';
                // console.log(saleData.amount);
                // console.log(saleData.grand_total);
                if (saleData.amount > saleData.grand_total || saleData.amount < 0) {
                    $('#payment-errors').html(
                        'Amount paid cannot be negative or greater than the total payable.').show();
                    return;
                }

                paymentModal.hide();
                console.log(saleData);
                submitSale("{{ route('sales.store.withPayment') }}", saleData, $('#pay-now-btn'));

            });

            // Helper function to find the tax ID from the rate (since the order object only stores the rate)
            function findTaxIdByRate(rateToFind) {
                const taxes = {!! Js::from($taxes->pluck('id', 'rate')) !!};
                return taxes[rateToFind.toFixed(2)] || null;
            }

            // Helper functions to ensure consistent calculations
            function calculateSubTotal() {
                return Object.values(order.items).reduce((sum, item) => sum + (item.price * item.quantity), 0);
            }

            function calculateOrderTaxAmount(subTotal, taxRate) {
                return subTotal * (taxRate / 100);
            }

            function calculateGrandTotal() {
                /*
                const subTotal = calculateSubTotal();
                const taxAmount = calculateOrderTaxAmount(subTotal, order.tax_rate);
                let discountAmount = (order.discount_type === 'percentage') ? subTotal * (order.discount / 100) :
                    order.discount;
                const grandTotal = subTotal + taxAmount + order.shipping - discountAmount;
                return grandTotal;
                */
                const subTotal = calculateSubTotal(); // Assumes this returns a float
                const taxAmount = calculateOrderTaxAmount(subTotal, order.tax_rate); // Assumes this returns a float
                const shipping = parseFloat(order.shipping) || 0;
                const discountValue = parseFloat(order.discount) || 0;

                // 2. Calculate the discount amount
                let discountAmount = (order.discount_type === 'percentage') ?
                    subTotal * (discountValue / 100) :
                    discountValue;

                // 3. Calculate the raw grand total
                const rawGrandTotal = subTotal + taxAmount + shipping - discountAmount;

                // --- THIS IS THE FIX ---
                // 4. Round the result to 2 decimal places and ensure it's a float.
                //    Math.round(num * 100) / 100 is the standard way to do this.
                const roundedGrandTotal = Math.round(rawGrandTotal * 100) / 100;

                return roundedGrandTotal;
            }

        });
    </script>
@endpush
