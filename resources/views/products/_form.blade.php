<div class="card shadow-sm">
    <div class="card-body">
        {{-- Product Type Selector --}}
        @include('layouts._messages')
        <div class="mb-4">
            <label class="form-label">Product Type</label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="type_single" value="single"
                        @checked(old('type', $product->type ?? 'single') == 'single') onchange="toggleProductType()" />

                    <label class="form-check-label" for="type_single">Single Product</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="type_variation" value="variation"
                        @checked(old('type', $product->type) == 'variation') onchange="toggleProductType()">
                    <label class="form-check-label" for="type_variation">Variation Product</label>
                </div>
            </div>
        </div>

        {{-- 1. General Information (Always Visible) --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                <select name="supplier_id" class="form-select">
                    <option value="">Select Supplier</option>
                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id }}" @selected(old('supplier_id', $product->supplier_id) == $s->id)>{{ $s->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Name Product <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}">
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select">
                    <option value="">Select Category</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
                        @if ($c->children->isNotEmpty())
                            @foreach ($c->children as $child)
                                <option value="{{ $child->id }}" @selected(old('category_id', $product->category_id) == $child->id)>&nbsp;&nbsp;&nbsp;›
                                    {{ $child->name }}</option>
                            @endforeach
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <hr>

        {{-- 2. SINGLE PRODUCT FIELDS (Toggled) --}}
        <div id="single-product-fields">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Product Image</label>

                    {{-- START: NEW IMAGE UPLOADER --}}
                    <div class="image-uploader" id="imageUploader">
                        {{-- This input is hidden but handles the file selection --}}
                        <input type="file" name="product_image" id="product_image_input" accept="image/*">

                        {{-- 1. Image Preview (or Default Image) --}}
                        @php
                            $defaultImage = asset('public/storage/images/default_image.png'); // Path to your default placeholder
                            $existingImage = $product->product_image
                                ? asset('public/storage/' . $product->product_image)
                                : $defaultImage;
                        @endphp
                        <img src="{{ $existingImage }}" alt="Preview" class="image-preview" id="imagePreview">

                        {{-- 2. Initial Upload Text (only shown if no image) --}}
                        <div class="upload-text" id="uploadText"
                            style="{{ $product->product_image ? 'display:none;' : '' }}">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Upload Image</span>
                        </div>

                        {{-- 3. Hover Overlay with Camera Icon --}}
                        <div class="hover-overlay">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                    {{-- END: NEW IMAGE UPLOADER --}}
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-12 mb-3"><label class="form-label">SKU <span
                                    class="text-danger">*</span></label><input type="text" name="sku"
                                class="form-control" value="{{ old('sku', $product->sku) }}"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Measurement</label><input type="text"
                                name="measurement" class="form-control"
                                value="{{ old('measurement', $product->measurement) }}"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Cbm</label><input type="number"
                                step="0.0001" name="cbm" class="form-control"
                                value="{{ old('cbm', $product->cbm) }}"></div>
                        <div class="col-md-12 mb-3"><label class="form-label">Weight</label>
                            <div class="input-group"><input type="number" step="0.01" name="weight"
                                    class="form-control" value="{{ old('weight', $product->weight) }}"><span
                                    class="input-group-text">Kg</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <h5 class="mt-4">Price & Tax</h5>
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Tax</label>
                    <div class="input-group">
                        {{-- Add id and class --}}
                        <select name="tax_id" id="tax_id" class="form-select calculation-trigger">
                            <option value="" data-rate="0">Select Tax...</option>
                            @foreach ($taxes as $tax)
                                <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}"
                                    @selected(old('tax_id', $product->tax_id) == $tax->id)>{{ $tax->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary" type="button">+</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Purchase Price <span class="text-danger">*</span></label>
                    {{-- Add id and class --}}
                    <input type="number" step="0.01" name="purchase_price" id="purchase_price"
                        class="form-control calculation-trigger"
                        value="{{ old('purchase_price', $product->purchase_price) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Margin</label>
                    <div class="input-group">
                        {{-- Add id and class --}}
                        <input type="number" step="0.01" name="margin" id="margin"
                            class="form-control calculation-trigger" placeholder="Enter Number"
                            value="{{ old('margin', $product->margin) }}">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sale Price <span class="text-danger">*</span></label>
                    {{-- Add id --}}
                    <input type="number" step="0.01" name="sale_price" id="sale_price" class="form-control"
                        value="{{ old('sale_price', $product->sale_price) }}">
                </div>
            </div>
        </div>

        {{-- 3. VARIATION PRODUCT FIELDS (Toggled) --}}
        <div id="variation-product-fields" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Variation - Price & Tax</h5>
                <button type="button" onclick="addVariationRow()" class="btn btn-success">Add Option</button>
            </div>
            <div id="variation-container">
                @if (isset($product) && $product->variations->isNotEmpty())
                    @foreach ($product->variations as $index => $variation)
                        @include('products._variation_row', [
                            'index' => $index,
                            'variation' => $variation,
                            'taxes' => $taxes,
                        ])
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>


@push('scripts')
    <script>
        // Use the standard jQuery document ready function
        $(document).ready(function() {

            let variationIndex = {{ isset($product) && $product->variations ? $product->variations->count() : 0 }};

            // --- Function to add a new variation row ---
            window.addVariationRow = function() {
                const container = $('#variation-container');
                const template = $('#variation-template').html();
                const newRowHtml = template.replace(/__INDEX__/g, variationIndex);
                container.append(newRowHtml);
                // No extra JS needed here, the delegated listeners will handle it
                variationIndex++;
            }

            // --- Function to toggle between single/variation product forms ---
            window.toggleProductType = function() {
                const type = $('input[name="type"]:checked').val();
                const singleFields = $('#single-product-fields');
                const variationFields = $('#variation-product-fields');
                const variationContainer = $('#variation-container');

                if (type === 'single') {
                    singleFields.show();
                    variationFields.hide();
                    singleFields.find(
                        'input[name="sku"], input[name="purchase_price"], input[name="sale_price"]').prop(
                        'required', true);
                } else {
                    singleFields.hide();
                    variationFields.show();
                    singleFields.find(
                        'input[name="sku"], input[name="purchase_price"], input[name="sale_price"]').prop(
                        'required', false);
                    if (variationContainer.children().length === 0) {
                        addVariationRow();
                    }
                }
            }

            // --- FORWARD CALCULATION (Purchase Price or Margin -> Sale Price) ---
            function calculateSalePrice(rowContext) {
                const purchasePriceEl = $(rowContext).find('input[name*="purchase_price"]');
                const marginEl = $(rowContext).find('input[name*="margin"]');
                const salePriceEl = $(rowContext).find('input[name*="sale_price"]');

                const purchasePrice = parseFloat(purchasePriceEl.val()) || 0;
                const marginPercent = parseFloat(marginEl.val()) || 0;

                const marginAmount = purchasePrice * (marginPercent / 100);
                const finalSalePrice = purchasePrice + marginAmount;

                salePriceEl.val(finalSalePrice.toFixed(2));
            }

            // --- REVERSE CALCULATION (Sale Price -> Margin) ---
            function calculateMargin(rowContext) {
                const purchasePriceEl = $(rowContext).find('input[name*="purchase_price"]');
                const marginEl = $(rowContext).find('input[name*="margin"]');
                const salePriceEl = $(rowContext).find('input[name*="sale_price"]');

                const purchasePrice = parseFloat(purchasePriceEl.val()) || 0;
                const salePrice = parseFloat(salePriceEl.val()) || 0;

                if (purchasePrice > 0) {
                    const marginPercent = ((salePrice - purchasePrice) / purchasePrice) * 100;
                    marginEl.val(marginPercent.toFixed(2));
                } else {
                    // Avoid division by zero if purchase price is 0
                    marginEl.val('0.00');
                }
            }

            // --- JQUERY EVENT LISTENERS ---

            // Listener for the radio buttons
            $('input[name="type"]').on('change', toggleProductType);

            // Delegated listener for the entire form
            $('form').on('input', function(event) {
                const target = $(event.target);
                const row = target.closest('.variation-row, #single-product-fields');

                // Check which field was changed
                if (target.is('input[name*="purchase_price"]') || target.is('input[name*="margin"]')) {
                    // If Purchase Price or Margin changed, calculate Sale Price
                    calculateSalePrice(row);
                } else if (target.is('input[name*="sale_price"]')) {
                    // If Sale Price changed, calculate Margin
                    calculateMargin(row);
                }
            });

            // --- INITIALIZATION ---
            // Run toggle on page load to set the correct view.
            toggleProductType();

            // Initial calculation for existing rows on page load
            /*
            $('.variation-row').each(function() {
                calculateSalePrice(this);
            });
            if ($('#single-product-fields').is(':visible')) {
                calculateSalePrice($('#single-product-fields'));
            }
            */

            let activeTaxSelect = null;

            // 1. Open the modal when a '+' button is clicked
            $('form').on('click', '.btn-outline-primary', function() {
                // Find the <select> element right next to the clicked button
                activeTaxSelect = $(this).prev('select');
                $('#addTaxModal').modal('show');
            });

            // 2. Handle the AJAX form submission
            $('#addTaxForm').on('submit', function(e) {
                e.preventDefault(); // Stop the normal form submission
                const form = $(this);
                const url = "{{ route('taxes.ajaxStore') }}";
                const data = form.serialize();

                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            const newTax = response.tax;

                            // Create a new <option> element
                            const newOption =
                                `<option value="${newTax.id}" data-rate="${newTax.rate}">${newTax.name}</option>`;

                            // Add the new option to ALL tax dropdowns on the page
                            $('select[name*="tax_id"]').append(newOption);

                            // If we know which dropdown triggered this, select the new option in it
                            if (activeTaxSelect) {
                                activeTaxSelect.val(newTax.id);
                                // Manually trigger the 'change' event to update margin and price
                                activeTaxSelect.trigger('change');
                            }

                            // Close the modal and reset the form
                            $('#addTaxModal').modal('hide');
                            form[0].reset();
                            $('#tax-errors').hide();
                        }
                    },
                    error: function(xhr) {
                        // Handle validation errors
                        const errors = xhr.responseJSON.errors;
                        const errorContainer = $('#tax-errors');
                        errorContainer.html(''); // Clear previous errors
                        if (errors) {
                            errors.forEach(function(error) {
                                errorContainer.append('<div>' + error + '</div>');
                            });
                            errorContainer.show();
                        }
                    }
                });
            });
            /*
            const imageInput = $('#product_image_input');
            const imagePreview = $('#imagePreview');
            const uploadText = $('#uploadText');

            // This is the click handler for the file input itself.
            // It's crucial for stopping the infinite loop.
            imageInput.on('click', function(event) {
                // Stop the click from bubbling up to the parent container.
                event.stopPropagation();
            });

            // This handler is for the visual container div.
            $('#imageUploader').on('click', function() {
                // When the user clicks the box, programmatically click the hidden file input.
                imageInput.trigger('click');
            });

            // This handler fires when the user selects a file.
            imageInput.on('change', function(event) {
                const file = event.target.files[0];

                if (file) {
                    // Use FileReader to read the selected file and create a preview.
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Set the src of the image preview to the new file's data URL.
                        imagePreview.attr('src', e.target.result);
                        // Hide the initial "Upload Image" text, as we now have a preview.
                        uploadText.hide();
                    };
                    reader.readAsDataURL(file);
                }
            });
            */
            // Use event delegation on a static parent (the form)
            $('form').on('click', '.image-uploader', function(event) {
                // Find the specific file input within the clicked uploader
                $(this).find('.variation-image-input, #product_image_input').trigger('click');
            });

            $('form').on('click', '.variation-image-input, #product_image_input', function(event) {
                // Stop the click from bubbling up to the parent container to prevent the infinite loop
                event.stopPropagation();
            });

            // Delegated listener for the 'change' event on any file input
            $('form').on('change', '.variation-image-input, #product_image_input', function(event) {
                const file = event.target.files[0];
                // Find the parent uploader container of the changed input
                const uploader = $(this).closest('.image-uploader');
                const imagePreview = uploader.find('.image-preview');
                const uploadText = uploader.find('.upload-text');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        uploadText.hide();
                    };
                    reader.readAsDataURL(file);
                }
            });

        });
    </script>

    {{-- The hidden template remains the same --}}
    <script type="text/template" id="variation-template">
    @include('products._variation_row', ['index' => '__INDEX__', 'taxes' => $taxes, 'variation' => null])
</script>
@endpush
