@extends('layouts.app')
@section('title', 'My Profile')

@push('styles')
    <style>
        /* Styles to match your new design */
        .profile-body .profile-pic-upload {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .profile-body .profile-pic {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .profile-body .profile-pic .close {
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            width: 24px;
            height: 24px;
            line-height: 24px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.2s;
            cursor: pointer;
        }

        .profile-body .profile-pic:hover .close {
            opacity: 1;
        }

        .profile-body .image-upload input[type="file"] {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Profile</h4>
                    <h6>User Profile</h6>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Profile</h4>
                </div>
                <div class="card-body profile-body">
                    @include('layouts._messages')

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <h5 class="mb-3"><i class="ti ti-user text-primary me-1"></i>Basic Information</h5>

                        {{-- Profile Picture Section --}}
                        <div class="profile-pic-upload">
                            <div class="profile-pic p-2">
                                @php
                                    $profilePictureUrl = $user->profile_picture
                                        ? asset('storage/' . $user->profile_picture)
                                        : asset('storage/images/default_avatar.png');
                                @endphp
                                <img src="{{ $profilePictureUrl }}" class="object-fit-cover w-100 h-100 rounded-1"
                                    alt="user" id="profile-image-preview">
                                <button type-="button" class="close rounded-1" id="remove-image-btn"
                                    style="display: {{ $user->profile_picture ? 'block' : 'none' }};">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                {{-- This hidden input will tell the backend to remove the image --}}
                                <input type="hidden" name="remove_profile_picture" id="remove-profile-picture-input"
                                    value="0">
                            </div>
                            <div class="mb-3">
                                <div class="image-upload mb-0 d-inline-flex">
                                    <label for="profile_picture_input" class="btn btn-primary fs-13">Change Image</label>
                                    <input type="file" id="profile_picture_input" name="profile_picture"
                                        accept="image/*">
                                </div>
                                <p class="mt-2 text-muted small">Upload an image below 2 MB. Accepted formats: JPG, PNG</p>
                            </div>
                        </div>

                        {{-- Form Fields Section --}}
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Full Name<span class="text-danger ms-1">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Email<span class="text-danger ms-1">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <div class="pass-group">
                                        <input type="password" name="password" class="pass-input form-control">
                                        <span class="fas toggle-password fa-eye-slash"></span>
                                    </div>
                                    <small class="text-muted">Leave blank to keep your current password.</small>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <div class="pass-group">
                                        <input type="password" name="password_confirmation" class="pass-input form-control">
                                        <span class="fas toggle-password fa-eye-slash"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const fileInput = $('#profile_picture_input');
            const previewImg = $('#profile-image-preview');
            const removeBtn = $('#remove-image-btn');
            const removeInput = $('#remove-profile-picture-input');
            const defaultAvatar = "{{ asset('storage/images/default_avatar.png') }}";

            // Show preview of newly selected image
            fileInput.on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.attr('src', e.target.result);
                        removeBtn.show();
                        removeInput.val('0'); // If a new image is chosen, we don't want to remove it
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Handle the remove image button
            removeBtn.on('click', function() {
                previewImg.attr('src', defaultAvatar); // Revert to default avatar
                fileInput.val(''); // Clear the file input
                removeInput.val('1'); // Set hidden input value to 1 to signal removal
                $(this).hide();
            });
        });
    </script>
@endpush
