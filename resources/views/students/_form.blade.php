<div class="card">
    <div class="card-body">
        <div class="row">
            <h5 class="mb-3">Login Information</h5>
            <div class="col-md-6 mb-3">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $student->user->name ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $student->user->email ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Password @if(!isset($student))<span class="text-danger">*</span>@endif</label>
                <input type="password" name="password" class="form-control">
                @if(isset($student))<small class="text-muted">Leave blank to keep current password.</small>@endif
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            <hr class="my-4">

            <h5 class="mb-3">Student Information</h5>
            <div class="col-md-6 mb-3">
                <label class="form-label">Roll Number <span class="text-danger">*</span></label>
                <input type="text" name="roll_number" class="form-control" value="{{ old('roll_number', $student->roll_number ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Class <span class="text-danger">*</span></label>
                <input type="text" name="class_name" class="form-control" value="{{ old('class_name', $student->class_name ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Parent's Name</label>
                <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name', $student->parent_name ?? '') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $student->phone_number ?? '') }}">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control">{{ old('address', $student->address ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>