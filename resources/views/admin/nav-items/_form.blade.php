<div class="card shadow-sm">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $navItem->name ?? '') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="type" class="form-label">Item Type <span class="text-danger">*</span></label>
                <select name="type" id="itemType" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="header" @selected(old('type', $navItem->type ?? '') == 'header')>Header</option>
                    <option value="link" @selected(old('type', $navItem->type ?? '') == 'link')>Link</option>
                    <option value="dropdown" @selected(old('type', $navItem->type ?? '') == 'dropdown')>Dropdown</option>
                </select>
                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            {{-- This entire div will be shown/hidden by JavaScript --}}
            <div id="parentItemDiv" class="col-md-12 mb-3">
                <label for="parent_id" class="form-label">Parent Item</label>
                <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                    <option value="">-- Select a Parent --</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" data-type="{{ $parent->type }}"
                            @selected(old('parent_id', $navItem->parent_id ?? '') == $parent->id)>
                            {{ $parent->name }} ({{ ucfirst($parent->type) }})
                        </option>
                    @endforeach
                </select>
                @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="order" class="form-label">Order <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('order') is-invalid @enderror" name="order" value="{{ old('order', $navItem->order ?? 0) }}" required>
                @error('order') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- This div will be shown/hidden for link types --}}
            <div id="routeDiv" class="col-md-6 mb-3">
                <label for="route" class="form-label">Route</label>
                <input type="text" class="form-control @error('route') is-invalid @enderror" name="route" value="{{ old('route', $navItem->route ?? '') }}">
                <small class="text-muted">e.g., `admin.users.index`. Required for 'Link' type.</small>
                @error('route') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-12 mb-3">
                <label for="icon" class="form-label">Icon</label>
                <input type="text" class="form-control" name="icon" value="{{ old('icon', $navItem->icon ?? '') }}">
                <small class="text-muted">e.g., `ti-user`. (tabler icons class)</small>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <a href="{{ route('admin.nav-items.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemTypeSelect = document.getElementById('itemType');
    const parentItemDiv = document.getElementById('parentItemDiv');
    const routeDiv = document.getElementById('routeDiv');

    function toggleFields() {
        const selectedType = itemTypeSelect.value;

        // Logic for Parent Item visibility
        if (selectedType === 'header') {
            parentItemDiv.style.display = 'none';
        } else {
            parentItemDiv.style.display = 'block';
        }

        // Logic for Route visibility
        if (selectedType === 'link') {
            routeDiv.style.display = 'block';
        } else {
            // Dropdowns and Headers do not have direct routes
            routeDiv.style.display = 'none';
        }
    }

    // Run on page load
    toggleFields();

    // Run whenever the type changes
    itemTypeSelect.addEventListener('change', toggleFields);
});
</script>
@endpush