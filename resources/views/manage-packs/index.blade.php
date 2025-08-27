@extends('layouts.app')

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

    {{-- Include the "Add Product" Modal (it's the same one) --}}
    @include('manage-packs._add-product-modal', ['allProducts' => $allProducts])
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
                                const newRowHtml = `
                            <tr data-id="${product.id}">
                                <td>${product.name}</td>
                                <td>${product.sku || '(Variation Parent)'}</td>
                                <td>${product.supplier && product.supplier.user ? product.supplier.user.name : 'N/A'}</td>
                                <td>${product.type === 'variation' ? `<button class="btn btn-sm variant-btn"><i class="ti ti-checkbox"></i></button>` : `<button class="btn btn-sm btn-outline-info disabled" disabled><i class="ti ti-checkbox"></i></button>`}</td>
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
        });
    </script>
@endpush
