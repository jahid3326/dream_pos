<div class="variation-row border p-3 mb-3 rounded position-relative" id="variation-row-{{ $index }}">
    {{-- Hidden input for existing variation ID --}}
    @if (isset($variation))
        <input type="hidden" name="variations[{{ $index }}][id]" value="{{ $variation->id }}">
    @endif

    <div class="row">
        <div class="col-md-2">
            <label class="form-label">Upload</label>

            {{-- NEW: Interactive Image Uploader for Variations --}}
            {{-- We use the variation's index to create unique IDs --}}
            <div class="image-uploader" id="imageUploader_{{ $index }}">
                <input type="file" name="variations[{{ $index }}][image]"
                    id="variation_image_input_{{ $index }}" class="variation-image-input" accept="image/*">

                @php
                    $defaultImage = asset('storage/images/default_image.png');
                    $imageSrc =
                        isset($variation) && $variation->image
                            ? asset('storage/' . $variation->image)
                            : $defaultImage;
                @endphp

                <img src="{{ $imageSrc }}" alt="Preview" class="image-preview">

                <div class="upload-text" style="{{ isset($variation) && $variation->image ? 'display:none;' : '' }}">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Upload</span>
                </div>

                <div class="hover-overlay">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
        </div>
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Measurement*</label><input type="text"
                        name="variations[{{ $index }}][measurement]" class="form-control"
                        value="{{ old('variations.' . $index . '.measurement', $variation->measurement ?? '') }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Cbm*</label><input type="number" step="0.0001"
                        name="variations[{{ $index }}][cbm]" class="form-control"
                        value="{{ old('variations.' . $index . '.cbm', $variation->cbm ?? '') }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">SKU*</label><input type="text"
                        name="variations[{{ $index }}][sku]" class="form-control"
                        value="{{ old('variations.' . $index . '.sku', $variation->sku ?? '') }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Weight*</label>
                    <div class="input-group"><input type="number" step="0.01"
                            name="variations[{{ $index }}][weight]" class="form-control"
                            value="{{ old('variations.' . $index . '.weight', $variation->weight ?? '') }}"><span
                            class="input-group-text">Kg</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-3"><label class="form-label">Tax</label>
            <div class="input-group">
                <select name="variations[{{ $index }}][tax_id]" class="form-select calculation-trigger">
                    <option value="" data-rate="0">None</option>
                    @foreach ($taxes as $tax)
                        {{-- Make sure data-rate attribute is here --}}
                        <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}"
                            @selected(old('variations.' . $index . '.tax_id', $variation->tax_id ?? '') == $tax->id)>{{ $tax->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-outline-primary" type="button">+</button>
            </div>
        </div>
        <div class="col-md-3"><label class="form-label">Purchase Price*</label><input type="number" step="0.01"
                name="variations[{{ $index }}][purchase_price]" class="form-control calculation-trigger"
                value="{{ old('variations.' . $index . '.purchase_price', $variation->purchase_price ?? '') }}"></div>
        <div class="col-md-3"><label class="form-label">Margin</label>
            <div class="input-group"><input type="number" step="0.01"
                    name="variations[{{ $index }}][margin]" class="form-control calculation-trigger"
                    value="{{ old('variations.' . $index . '.margin', $variation->margin ?? '') }}"><span
                    class="input-group-text">%</span></div>
        </div>
        <div class="col-md-3"><label class="form-label">Sale Price*</label><input type="number" step="0.01"
                name="variations[{{ $index }}][sale_price]" class="form-control"
                value="{{ old('variations.' . $index . '.sale_price', $variation->sale_price ?? '') }}"></div>
    </div>
    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
        onclick="this.closest('.variation-row').remove()">×</button>
</div>
