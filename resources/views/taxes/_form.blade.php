<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Tax Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $tax->name ?? '') }}"
                    required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="rate" class="form-label">Rate (%) <span class="text-danger">*</span></label>
                <input type="number" name="rate" class="form-control" step="0.01"
                    value="{{ old('rate', $tax->rate ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1" @selected(old('status', $tax->status ?? 1) == 1)>Enabled</option>
                    <option value="0" @selected(old('status', $tax->status ?? 1) == 0)>Disabled</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <a href="{{ route('taxes.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
