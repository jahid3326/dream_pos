<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    value="{{ old('name', $user->name ?? '') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                    name="email" value="{{ old('email', $user->email ?? '') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                    name="password" {{ isset($user) ? '' : 'required' }}>
                @if (isset($user))
                    <small class="form-text text-muted">Leave blank to keep the current password.</small>
                @endif
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
            </div>
            <div class="col-md-6 mb-3">
                <label for="role_id" class="form-label">Role</label>
                <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id"
                    required>
                    <option value="">Select a Role</option>

                    {{-- This loop requires the $roles variable to exist --}}
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach

                </select>
                @error('role_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input class="form-control @error('profile_picture') is-invalid @enderror" type="file"
                    id="profile_picture" name="profile_picture">
                @error('profile_picture')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if (isset($user) && $user->profile_picture)
                    <div class="mt-2">
                        <img src="{{ asset('public/storage/' . $user->profile_picture) }}" alt="Current Picture"
                            width="100">
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
