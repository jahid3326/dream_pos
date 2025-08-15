<div class="card shadow-sm">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name ?? '') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="parent_id" class="form-label">Parent Category</label>
                <select id="parent_id" name="parent_id" class="form-select">
                    <option value="">-- None (Top-Level Category) --</option>
                    @foreach($parentCategories as $pCat)
                        <option value="{{ $pCat->id }}" @selected(old('parent_id', $category->parent_id ?? '') == $pCat->id)>
                            {{ $pCat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12 mb-3">
                <label for="logo" class="form-label">Category Logo</label>
                <input type="file" id="logo" name="logo" class="form-control @error('logo') is-invalid @enderror">
                @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if(isset($category) && $category->logo)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $category->logo) }}" alt="Current Logo" class="img-thumbnail" width="150">
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>