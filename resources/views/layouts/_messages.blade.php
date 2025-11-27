{{-- This file will display any session-based messages --}}

{{-- 1. General Success Message --}}
@if (session('success'))
    <div class="alert alert-solid-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="fas fa-xmark"></i></button>
    </div>
@endif

{{-- 2. General Error Message --}}
@if (session('error'))
    <div class="alert alert-solid-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="fas fa-xmark"></i></button>
    </div>
@endif

{{-- 3. General Warning Message --}}
@if (session('warning'))
    <div class="alert alert-solid-warning alert-dismissible fade show">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="fas fa-xmark"></i></button>
    </div>
@endif

{{-- 4. General Info Message --}}
@if (session('info'))
    <div class="alert alert-solid-info alert-dismissible fade show">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="fas fa-xmark"></i></button>
    </div>
@endif

{{-- 5. Validation Errors --}}
{{-- This checks if there are any validation errors in the session --}}
@if ($errors->any())
    <div class="alert alert-solid-danger alert-dismissible fade show">
        <strong>Whoops! Something went wrong.</strong>
        <ul class="mt-2 mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="fas fa-xmark"></i></button>
    </div>
@endif