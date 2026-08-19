<div class="card shadow-sm">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <!-- User Details -->
                    <div class="col-md-6 mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name', $supplier->user->name ?? '') }}" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Phone Number <span class="text-danger">*</span></label><input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $supplier->phone_number ?? '') }}" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Company Name</label><input type="text" name="company_name" class="form-control" value="{{ old('company_name', $supplier->company_name ?? '') }}"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Email <span class="text-danger">*</span></label><input type="email" name="email" class="form-control" value="{{ old('email', $supplier->user->email ?? '') }}" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Password @if(!isset($supplier))<span class="text-danger">*</span>@endif</label><input type="password" name="password" autocomplete="off" class="form-control"><small class="text-muted">Leave blank to keep current password.</small></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Tax Number</label><input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $supplier->tax_number ?? '') }}"></div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Status and Profile Image -->
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" @selected(old('status', $supplier->status ?? 1) == 1)>Enabled</option>
                        <option value="0" @selected(old('status', $supplier->status ?? 1) == 0)>Disabled</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Profile Image</label>
                    <input type="file" name="profile_picture" class="form-control">
                    @if(isset($supplier) && $supplier->user->profile_picture)
                    <img src="{{ asset('storage/' . $supplier->user->profile_picture) }}" alt="Profile" class="img-thumbnail mt-2" width="120">
                    @endif
                </div>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Billing Address</label>
                <textarea name="billing_address" class="form-control" rows="3">{{ old('billing_address', $supplier->billing_address ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>