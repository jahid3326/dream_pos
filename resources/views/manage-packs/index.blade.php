@extends('layouts.app')
@section('title', 'Manage Pack')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Pack</h4>
                        <h6>Manage your packs</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('Pack', 'create'))
                        <a href="{{ route('packs.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add
                            New Pack</a>
                    @endif
                </div>
            </div>

            @include('layouts._messages')

            <div class="accordion" id="packsAccordion">
                @forelse($packs as $pack)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingPack{{ $pack->id }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapsePack{{ $pack->id }}">
                                <div class="d-flex w-100 justify-content-between align-items-center pe-3">
                                    <span><strong>Pack Name:</strong> {{ $pack->name }}</span>
                                    <span><strong>Created At:</strong> {{ $pack->created_at->format('d-m-Y h:i a') }}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapsePack{{ $pack->id }}" class="accordion-collapse collapse"
                            data-bs-parent="#packsAccordion">
                            <div class="accordion-body">
                                {{-- Include partial for groups --}}
                                @include('manage-packs._groups-accordion', ['groups' => $pack->groups])
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card">
                        <div class="card-body text-center p-4">No packs found to manage.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Include the "Add Product" Modal --}}
    @include('manage-packs._add-product-modal', ['allProducts' => $allProducts])

    {{-- Include the "Show pack product variation" Modal --}}
    @include('manage-packs._pack-product-variation-modal')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    {{-- The JavaScript is exactly the same as the previous step --}}
    <script>
        $(document).ready(function() {
            // =========================================================================
            // CONFIG & STATE
            // =========================================================================
            // Initialize the Bootstrap 5 modal instance
            const addProductModal = new bootstrap.Modal(document.getElementById('addProductModal'));
            const manageItemsModal = new bootstrap.Modal(document.getElementById('manageItemsModal'));

            let existingProductIds = [];

            // =========================================================================
            // SORTABLEJS INITIALIZATION
            // =========================================================================
            function initSortable(element) {
                const elements = element ? [element] : document.querySelectorAll('.product-sortable');
                elements.forEach(function(tbody) {
                    if (tbody.sortableInstance) {
                        tbody.sortableInstance.destroy();
                    }
                    tbody.sortableInstance = new Sortable(tbody, {
                        handle: '.handle',
                        animation: 150,
                        onEnd: function(evt) {
                            const optionId = $(evt.target).data('option-id');
                            const order = [];
                            $(evt.target).find('tr').each(function(index) {
                                order.push({
                                    id: $(this).data('id'),
                                    position: index + 1
                                });
                            });

                            $.ajax({
                                url: `/manage-packs/options/${optionId}/products/reorder`,
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    order: order
                                },
                                dataType: 'json'
                            });
                        }
                    });
                });
            }
            // Initialize for elements that exist on page load
            initSortable();

            // =========================================================================
            // MODAL FILTERING LOGIC
            // =========================================================================
            function applyModalFilters() {
                const searchTerm = $('#modal-product-search').val().toLowerCase();
                const categoryId = $('#modal-category-filter').val();

                $('#modal-product-table tbody tr').each(function() {
                    const row = $(this);
                    const productName = row.find('.product-name').text().toLowerCase();
                    const productCategoryId = row.data('category-id').toString();

                    // Check if the row matches the search term
                    const nameMatch = productName.includes(searchTerm);
                    // Check if the row matches the category filter
                    const categoryMatch = (categoryId === "" || productCategoryId === categoryId);

                    // Show the row only if it matches BOTH filters
                    if (nameMatch && categoryMatch) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });
            }

            // Attach listeners to the filter inputs
            $('#modal-product-search').on('keyup', applyModalFilters);
            $('#modal-category-filter').on('change', applyModalFilters);

            // =========================================================================
            // EVENT LISTENERS
            // =========================================================================

            /**
             * Event listener for opening the "Add Product" modal.
             * Uses event delegation to work for all "+ Add Product" buttons.
             */

            $('body').on('click', '.add-product-btn', function() {
                const optionId = $(this).data('option-id');

                // Store the current option's ID in a hidden input within the modal
                $('#current-option-id').val(optionId);

                // Reset filters when opening the modal
                $('#modal-product-search').val('');
                $('#modal-category-filter').val('');
                applyModalFilters(); // Apply empty filters to show all rows

                // --- THIS IS THE KEY LOGIC FOR PRE-CHECKING ---
                let existingProductIds = [];
                $(`#productList-${optionId} tbody tr`).each(function() {
                    const productId = $(this).data('id'); // Read the product ID from the row
                    if (productId) {
                        existingProductIds.push(productId.toString());
                    }
                });

                //    This is crucial to clear the state from the last time the modal was opened.
                $('#modal-product-table .product-checkbox').prop('checked', false).prop('disabled', false);

                //    then check and disable them.
                if (existingProductIds.length > 0) {
                    existingProductIds.forEach(function(id) {
                        $(`#modal-product-table .product-checkbox[value="${id}"]`).prop('checked',
                            true).prop('disabled', true);
                    });
                }

                addProductModal.show();
            });

            /**
             * Event listener for the "Save Products" button inside the modal.
             */

            $('#save-products-btn').on('click', function() {
                const optionId = $('#current-option-id').val();
                const saveButton = $(this);
                let newlySelectedProductIds = [];
                $('#modal-product-table .product-checkbox:checked:not(:disabled)').each(function() {
                    newlySelectedProductIds.push($(this).val());
                });

                if (newlySelectedProductIds.length === 0) {
                    addProductModal.hide();
                    return;
                }
                saveButton.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: `/manage-packs/options/${optionId}/products`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_ids: newlySelectedProductIds
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.products) {
                            const productListContainer = $(`#productList-${optionId}`);
                            let tableBody = productListContainer.find('tbody.product-sortable');
                            const wasEmpty = tableBody.length === 0;

                            if (wasEmpty) {
                                productListContainer.find('.text-muted').remove();
                                const tableHtml = `
                            <table class="table table-striped table-sm">
                                <thead><tr><th>Name</th><th>SKU</th><th>Supplier</th><th>Variation</th><th>Action</th><th style="width: 5%;">Order</th></tr></thead>
                                <tbody class="product-sortable" data-option-id="${optionId}"></tbody>
                            </table>`;
                                productListContainer.html(tableHtml);
                                tableBody = productListContainer.find('tbody.product-sortable');
                            }

                            response.products.forEach(function(product) {
                                const imageUrl = product.product_image ?
                                    `{{ asset('public/storage') }}/${product.product_image}` :
                                    `{{ asset('public/storage/images/default_image.png') }}`;
                                const newRowHtml = `
                            <tr data-id="${product.id}">
                                <td>
                                    <label class="d-flex align-items-center">
                                        <img src="${imageUrl}" alt="${product.name}"
                                            class="rounded me-2" width="40" height="40"
                                            style="object-fit: cover;">
                                        <div>
                                            <span class="fw-bold">${product.name}</span><br>
                                            <small class="text-muted">${product.measurement ?? 'N/A'}</small>
                                        </div>
                                    </label>
                                </td>
                                <td>${product.sku || '(Variation Parent)'}</td>
                                <td>${product.supplier && product.supplier.user ? product.supplier.user.name : 'N/A'}</td>
                                <td>${product.type === 'variation' ? `<button class="btn btn-sm variant-btn" data-pack-product-id="${product.pivot.id}"><i class="ti ti-checkbox"></i></button>` : `<button class="btn btn-sm btn-outline-info disabled" disabled><i class="ti ti-checkbox"></i></button>`}</td>
                                <td><button class="me-2 p-2 d-flex align-items-center border rounded remove-product-btn" data-product-id="${product.id}" data-option-id="${optionId}"><i data-feather="trash-2" class="feather-trash-2"></i></button></td>
                                <td><i class="fas fa-bars handle" style="cursor: move;"></i></td>
                            </tr>`;
                                tableBody.append(newRowHtml);
                            });

                            // --- FIX #1: ADD "REMOVE ALL" BUTTON IF IT WAS PREVIOUSLY EMPTY ---
                            if (wasEmpty) {
                                const buttonContainer = productListContainer.next('.mt-2');
                                if (buttonContainer.find('.remove-all-btn').length === 0) {
                                    buttonContainer.append(`
                                <button class="btn btn-outline-danger btn-sm remove-all-btn" data-option-id="${optionId}">Remove All</button>
                            `);
                                }
                            }

                            const viewButton = $(
                                `button[data-bs-target="#productsForOption${optionId}"]`);
                            const newCount = tableBody.find('tr').length;
                            viewButton.text(`View Products (${newCount})`);

                            initSortable(tableBody[0]);
                        }
                        addProductModal.hide();
                    },
                    error: function(xhr) {
                        alert('An error occurred. Please try again.');
                        console.error(xhr.responseText);
                    },
                    complete: function() {
                        saveButton.prop('disabled', false).text('Save Products');
                    }
                });
            });


            // --- REMOVE SINGLE PRODUCT ---
            $('body').on('click', '.remove-product-btn', function() {
                const button = $(this);
                const optionId = button.data('option-id');
                const productId = button.data('product-id');
                const tableRow = button.closest('tr');
                const tableBody = tableRow.closest('tbody');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
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
                        $.ajax({
                            url: `/manage-packs/options/${optionId}/products/${productId}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                tableRow.fadeOut(300, function() {
                                    $(this).remove();

                                    // --- FIX #2: CHECK IF THE LIST IS NOW EMPTY ---
                                    if (tableBody.find('tr').length === 0) {
                                        const productListContainer = $(
                                            `#productList-${optionId}`);
                                        productListContainer.html(
                                            '<p class="text-muted text-center">No products assigned to this option.</p>'
                                        );
                                        // Also remove the "Remove All" button for this group
                                        productListContainer.next('.mt-2').find(
                                            '.remove-all-btn').remove();
                                    }

                                    // Update the product count
                                    const viewButton = $(
                                        `button[data-bs-target="#productsForOption${optionId}"]`
                                    );
                                    const newCount = tableBody.find('tr')
                                        .length;
                                    viewButton.text(
                                        `View Products (${newCount})`);
                                });
                            }
                        });
                    }
                });
            });

            /**
             * Event listener for the "Remove All" button.
             */
            $('body').on('click', '.remove-all-btn', function() {
                const removeButton = $(this);
                const optionId = removeButton.data('option-id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
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
                        $.ajax({
                            url: `/manage-packs/options/${optionId}/products`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    const productListContainer = $(
                                        `#productList-${optionId}`);
                                    productListContainer.html(
                                        '<p class="text-muted text-center">No products assigned to this option.</p>'
                                    );
                                    removeButton.remove();
                                    const viewButton = $(
                                        `button[data-bs-target="#productsForOption${optionId}"]`
                                    );
                                    if (viewButton.length) {
                                        viewButton.text(`View Products (0)`);
                                    }
                                }
                            }
                        });
                    }
                });
            });

            /**
             * Event listener for the "Manage Variants" button on the main page.
             * Fetches data and opens the variation selection modal.
             */
            $('body').on('click', '.variant-btn', function() {
                const packProductId = $(this).data('pack-product-id');
                $('#current-pack-product-id').val(packProductId);

                const container = $('#items-checkbox-container');
                container.html(
                    '<tr><td colspan="3" class="text-center text-muted p-4">Loading...</td></tr>');
                manageItemsModal.show();

                $.ajax({
                    url: `{{ url('manage-packs/pack-products') }}/${packProductId}/data`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        // console.log(response);
                        $('#item-product-name').text(response.product_name);
                        container.empty();

                        if (response.all_selectable_items && response.all_selectable_items
                            .length > 0) {
                            response.all_selectable_items.forEach(function(item) {
                                // Check if this item's unique_id is in the list of already selected IDs
                                const isSelected = response.selected_ids.includes(item
                                    .unique_id);

                                // --- THIS IS THE KEY CHANGE ---
                                // Build the checked and disabled attributes based on the isSelected flag
                                const checkedAttribute = isSelected ? 'checked' : '';
                                const disabledAttribute = isSelected ? 'disabled' : '';

                                const rowHtml = `
                        <tr data-category-id="${item.category_id_for_filter}">
                            <td>
                                <input class="form-check-input item-checkbox" type="checkbox" 
                                       value="${item.unique_id}" id="item-${item.unique_id}" 
                                       ${checkedAttribute}>
                            </td>
                            <td class="item-name">
                                <label class="form-check-label d-flex align-items-center ${disabledAttribute ? 'text-muted' : ''}" for="item-${item.unique_id}">
                                    <img src="${item.image}" alt="${item.name}" class="rounded me-2"
                                    width="40" height="40" style="object-fit: cover;">
                                    <div>
                                    <span class="fw-bold">${item.display_name}</span><br><small class="text-muted">${item.measurement}</small>
                                    </div>
                                </label>
                            </td>
                            <td>${item.sku}</td>
                        </tr>`;
                                container.append(rowHtml);
                            });
                        } else {
                            container.html(
                                '<tr><td colspan="3" class="text-center text-muted p-4">No products available to add.</td></tr>'
                            );
                        }
                    },
                    error: function(xhr) {
                        container.html(
                            '<tr><td colspan="3" class="text-center text-danger">Failed to load items.</td></tr>'
                        );
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            });

            /**
             * Event listener for the "Save Selections" button inside the "Manage Items" modal.
             */
            $('#save-items-selections-btn').on('click', function() {
                const packProductId = $('#current-pack-product-id').val();
                const saveButton = $(this);
                let selectedItemIds = [];
                $('#items-checkbox-container .item-checkbox:checked').each(function() {
                    selectedItemIds.push($(this).val());
                });

                saveButton.prop('disabled', true).text('Saving...');

                let url =
                    `{{ route('manage-packs.pack-products.items.save', ['packProduct' => 'REPLACE_ID']) }}`;
                url = url.replace('REPLACE_ID', packProductId);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        item_ids: selectedItemIds
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            manageItemsModal.hide();
                            const button = $(
                                `.manage-items-btn[data-pack-product-id="${packProductId}"]`
                            );
                            button.text(`Manage Items (${response.count})`);
                        }
                    },
                    error: () => alert('An error occurred while saving.'),
                    complete: () => saveButton.prop('disabled', false).text('Save Selections')
                });
            });

            // --- FILTERS FOR THE "MANAGE ITEMS" MODAL ---
            function applyModalItemFilters() {
                const searchTerm = $('#modal-item-search').val().toLowerCase();
                const categoryId = $('#modal-item-category-filter').val();
                $('#items-checkbox-container tr').each(function() {
                    const row = $(this);
                    const itemName = row.find('.item-name').text().toLowerCase();
                    const itemCategoryId = row.data('category-id').toString();
                    const nameMatch = itemName.includes(searchTerm);
                    const categoryMatch = (categoryId === "" || itemCategoryId === categoryId);
                    row.toggle(nameMatch && categoryMatch);
                });
            }
            $('#modal-item-search').on('keyup', applyModalItemFilters);
            $('#modal-item-category-filter').on('change', applyModalItemFilters);

            // --- CHECK ALL FUNCTIONALITY ---
            $('#check-all-items').on('click', function() {
                // Check/uncheck only the VISIBLE checkboxes
                $('#items-checkbox-container tr:visible .item-checkbox').prop('checked', $(this).prop(
                    'checked'));
            });
        });
    </script>
@endpush
