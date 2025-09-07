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
                <div class="order-head bg-light d-flex align-items-center justify-content-between w-100 mb-3">
                    <div>
                        <h3>Order List</h3>
                        <span>Transaction ID : #65565</span>
                    </div>
                    <div>
                        <a class="link-danger fs-16" href="javascript:void(0);"><i class="ti ti-trash-x-filled"></i></a>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-icon-end position-relative">
                            <input type="text" class="form-control datetimepicker" placeholder="dd/mm/yyyy">
                            <span class="input-icon-addon">
                                <i class="ti ti-calendar text-gray-7"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Type Ref Number">
                    </div>
                    <div class="col-md-4">
                        <select class="select">
                            <option>Search Shop</option>
                            <option>IPhone 14 64GB</option>
                            <option>MacBook Pro</option>
                            <option>Rolex Tribute V3</option>
                            <option>Red Nike Angelo</option>
                            <option>Airpod 2</option>
                            <option>Oldest</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex align-items-center gap-2">
                            <div class="w-100">
                                <select class="select" name="customer_id">
                                    <option value="">Walk-in Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <a href="#" class="btn btn-primary btn-icon" data-bs-toggle="modal"
                                data-bs-target="#create"><i class="ti ti-user-plus"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product-added block-section">
                <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                    <h5 class="d-flex align-items-center mb-0">Order Details</h5>
                    <div class="badge bg-light text-gray-9 fs-12 fw-semibold py-2 border rounded">
                        Items : <span class="text-teal">3</span></div>
                </div>
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
                                    <th class="bg-transparent fw-bold">Product</th>
                                    <th class="bg-transparent fw-bold">QTY</th>
                                    <th class="bg-transparent fw-bold">Price</th>
                                    <th class="bg-transparent fw-bold">Sub Total</th>
                                    <th class="bg-transparent fw-bold text-end"></th>
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
                    <div class="col-sm-4">
                        <a href="javascript:void(0);"
                            class="btn btn-orange d-flex align-items-center justify-content-center w-100 mb-2"
                            data-bs-toggle="modal" data-bs-target="#hold-order"><i
                                class="ti ti-player-pause me-2"></i>Hold</a>
                        <a href="javascript:void(0);"
                            class="btn btn-secondary d-flex align-items-center justify-content-center w-100 mb-2"
                            data-bs-toggle="modal" data-bs-target="#orders"><i class="ti ti-shopping-cart me-2"></i>View
                            Orders</a>
                    </div>
                    <div class="col-sm-4">
                        <a href="javascript:void(0);"
                            class="btn btn-info d-flex align-items-center justify-content-center w-100 mb-2"><i
                                class="ti ti-trash me-2"></i>Void</a>
                        <a href="javascript:void(0);"
                            class="btn btn-indigo d-flex align-items-center justify-content-center w-100 mb-2"
                            data-bs-toggle="modal" data-bs-target="#reset"><i class="ti ti-reload me-2"></i>Reset</a>
                    </div>
                    <div class="col-sm-4">
                        <a href="javascript:void(0);"
                            class="btn btn-cyan d-flex align-items-center justify-content-center w-100 mb-2"
                            data-bs-toggle="modal" data-bs-target="#payment-completed"><i
                                class="ti ti-cash-banknote me-2"></i>Payment</a>
                        <a href="javascript:void(0);"
                            class="btn btn-danger d-flex align-items-center justify-content-center w-100 mb-2"
                            data-bs-toggle="modal" data-bs-target="#recents"><i
                                class="ti ti-refresh-dot me-2"></i>Transaction</a>
                    </div>
                </div>
            </div>
            <div class="block-section payment-method">
                <h5 class="mb-2">Select Payment</h5>
                <div class="row align-items-center justify-content-center methods g-2 mb-4">
                    <div class="col-sm-6 col-md-4 col-xl d-flex">
                        <a href="javascript:void(0);" class="payment-item flex-fill" data-bs-toggle="modal"
                            data-bs-target="#payment-cash">
                            <img src="{{ asset('public/assets/img/icons/cash-icon.svg') }}" alt="img">
                            <p class="fw-medium">Cash</p>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-xl d-flex">
                        <a href="javascript:void(0);" class="payment-item flex-fill" data-bs-toggle="modal"
                            data-bs-target="#payment-card">
                            <img src="{{ asset('public/assets/img/icons/card.svg') }}" alt="img">
                            <p class="fw-medium">Card</p>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-xl d-flex">
                        <a href="javascript:void(0);" class="payment-item flex-fill" data-bs-toggle="modal"
                            data-bs-target="#payment-points">
                            <img src="{{ asset('public/assets/img/icons/points.svg') }}" alt="img">
                            <p class="fw-medium">Points</p>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-xl d-flex">
                        <a href="javascript:void(0);" class="payment-item flex-fill" data-bs-toggle="modal"
                            data-bs-target="#payment-deposit">
                            <img src="{{ asset('public/assets/img/icons/deposit.svg') }}" alt="img">
                            <p class="fw-medium">Deposit</p>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-xl d-flex">
                        <a href="javascript:void(0);" class="payment-item flex-fill" data-bs-toggle="modal"
                            data-bs-target="#payment-cheque">
                            <img src="{{ asset('public/assets/img/icons/cheque.svg') }}" alt="img">
                            <p class="fw-medium">Cheque</p>
                        </a>
                    </div>
                </div>
                <div class="btn-block m-0">
                    <a class="btn btn-teal w-100" href="javascript:void(0);">
                        Pay : $56590.00
                    </a>
                </div>
            </div>
        </aside>
    </div>
    <!-- /Order Details -->
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {

            // =========================================================================
            // STATE & CACHE
            // =========================================================================
            const ORDER_STORAGE_KEY = 'pos_order';

            // localStorage.removeItem(ORDER_STORAGE_KEY);

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
                    tabsHtml +=
                        `<li id="${dataKey}-${item.id}" class="${className}" data-${dataKey}-id="${item.id}" data-id="${item.id}" data-name="${item[nameKey]}"><h6><a href="javascript:void(0);">${item[nameKey]}</a></h6></li>`;
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

            /** Renders the entire cart UI. */
            function renderCart() {
                cartBody.empty();
                let subTotal = 0;
                let totalItems = 0;

                if (Object.keys(order.items).length > 0) {
                    emptyCartMessage.hide();
                    cartTableContainer.show();
                } else {
                    emptyCartMessage.show();
                    cartTableContainer.hide();
                }

                for (const cartId in order.items) {
                    const item = order.items[cartId];
                    const itemSubTotal = item.price * item.quantity;
                    subTotal += itemSubTotal;
                    totalItems += item.quantity;

                    const rowHtml = `
                <tr data-cart-id="${cartId}">
                    <td><strong>${item.name}</strong><br><small class="text-muted">${item.category}</small></td>
                    <td><div class="qty-item m-0"><input type="number" class="form-control text-center cart-quantity" value="${item.quantity}" min="1"></div></td>
                    <td>$${item.price.toFixed(2)}</td>
                    <td>$${itemSubTotal.toFixed(2)}</td>
                    <td class="text-end"><a class="btn-icon delete-icon remove-item-btn" href="javascript:void(0);"><i class="ti ti-trash"></i></a></td>
                </tr>`;
                    cartBody.append(rowHtml);
                }

                syncActiveProductCards();
                calculateTotals(subTotal);
            }

            function calculateTotals(subTotal) {
                /*
                let subTotal = 0;
                for (const cartId in order.items) {
                    subTotal += order.items[cartId].price * order.items[cartId].quantity;
                }

                let discountAmount = (order.discount_type === 'percentage') ?
                    subTotal * (order.discount / 100) :
                    order.discount;

                const orderTaxAmount = subTotal * (order.tax_rate / 100);
                const grandTotal = subTotal + orderTaxAmount + order.shipping - discountAmount;

                $('#cart-subtotal').text(`$${subTotal.toFixed(2)}`);
                $('#cart-shipping').text(`$${order.shipping.toFixed(2)}`);
                $('#cart-tax').text(`$${orderTaxAmount.toFixed(2)}`);
                $('#tax-name-display').text(order.tax_name);
                $('#cart-discount').text(`-$${discountAmount.toFixed(2)}`);
                $('#cart-grandtotal').text(`$${grandTotal.toFixed(2)}`);

                const totalItems = Object.values(order.items).reduce((sum, item) => sum + item.quantity, 0);
                $('.badge:contains("Items") .text-teal').text(totalItems);
                $('.btn-block a').text(`Pay : $${grandTotal.toFixed(2)}`);
                */
            }

            function syncActiveProductCards() {
                $('.product-info').removeClass('active');
                for (const cartId in order.items) {
                    const item = order.items[cartId];
                    $(`.product-info[data-product-id="${item.id}"][data-variation-id="${item.variation_id || ''}"]`)
                        .addClass('active');
                }
            }

            /** Syncs checkboxes on product cards with the current cart state (for Pack Mode). */
            function syncPackProductCheckboxes() {
                $('.product-select-check').prop('checked', false);
                for (const cartId in order.items) {
                    const item = order.items[cartId];
                    $(`.product-info[data-product-id="${item.id}"][data-variation-id="${item.variation_id || ''}"]`)
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
                        <div class="col-sm-6 col-md-4 col-lg-4 product-item">
                            <div class="card h-100 product-info" 
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

                syncPackProductCheckboxes();
            }

            // --- "ADD ALL" BUTTON (for Pack Mode) ---
            packActionsContainer.on('click', function() {
                const activePackTab = level2Container.find('li.active');
                const activeSurfaceTab = level3Container.find('li.active');
                const activeOptionTab = level4Container.find('li.active');

                // Use .data() to safely get the IDs and names
                const packContext = {
                    pack_id: activePackTab.data('pack-id'),
                    pack_name: activePackTab.data('name'),
                    group_id: activeSurfaceTab.data('group-id'),
                    group_name: activeSurfaceTab.data('name'), // 'surface' is the name
                    option_id: activeOptionTab.data('option-id'),
                    option_name: activeOptionTab.data('name'),
                };

                // --- 2. LOG THE CONTEXT (for your requirement) ---
                console.log("--- Pack Context ---");
                console.log(packContext);

            });

            // --- CART ITEM INTERACTIONS ---
            cartBody.on('input', '.cart-quantity', function() {
                // Get the unique ID of the item from the parent <tr>
                const cartId = $(this).closest('tr').data('product-id');
                const newQuantity = parseInt($(this).val());

                if (order.items[cartId]) {
                    if (newQuantity > 0) {
                        // Update quantity in the cart object
                        order.items[cartId].quantity = newQuantity;
                    } else {
                        // Remove the item if quantity is 0 or less
                        delete order.items[cartId];
                    }
                    // Persist the changes and redraw the entire UI
                    saveOrderToStorage();
                    renderCart();
                }
            });
            cartBody.on('click', '.remove-item-btn', function() {
                // Get the unique ID of the item from the parent <tr>
                const cartId = $(this).closest('tr').data('product-id');

                // Remove the item from the cart object
                if (order.items[cartId]) {
                    delete order.items[cartId];
                    // Persist the changes and redraw the entire UI
                    saveOrderToStorage();
                    renderCart();
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
                order.shipping = parseFloat($('#modal-shipping-input').val()) || 0;
                saveOrderToStorage();
                calculateTotals();
                shippingModal.hide();
            });
            $('#discount-form').on('submit', function(e) {
                e.preventDefault();
                order.discount_type = $('#modal-discount-type').val();
                order.discount = parseFloat($('#modal-discount-value').val()) || 0;
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
            $('#shipping-cost').on('show.bs.modal', () => $('#modal-shipping-input').val(order.shipping));
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

        });
    </script>
@endpush
