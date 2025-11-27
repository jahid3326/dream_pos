<div class="header pos-header">

    <!-- Logo -->
    <div class="header-left active">
        <a href="{{ route('dashboard') }}" class="logo logo-normal">
            <img src="{{ asset('public/assets/img/logo.svg') }}" alt="Img">
        </a>
        <a href="{{ route('dashboard') }}" class="logo logo-white">
            <img src="{{ asset('public/assets/img/logo-white.svg') }}" alt="Img">
        </a>
        <a href="{{ route('dashboard') }}" class="logo-small">
            <img src="{{ asset('public/assets/img/logo-small.png') }}" alt="Img">
        </a>
    </div>
    <!-- /Logo -->

    <a id="mobile_btn" class="mobile_btn d-none" href="#sidebar">
        <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>

    <!-- Header Menu -->
    <ul class="nav user-menu">

        <!-- Search -->
        <li class="nav-item time-nav">

        </li>
        <!-- /Search -->

        <li class="nav-item pos-nav">
            <a href="{{ route('dashboard') }}" class="btn btn-purple btn-md d-inline-flex align-items-center">
                <i class="ti ti-world me-1"></i>Dashboard
            </a>
        </li>
        <li class="nav-item nav-item-box">
            <a href="javascript:void(0);" id="btnFullscreen" data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Maximize">
                <i class="ti ti-maximize"></i>
            </a>
        </li>
        <li class="nav-item dropdown has-arrow main-drop profile-nav">
            <a href="javascript:void(0);" class="nav-link userset" data-bs-toggle="dropdown">
                <span class="user-info p-0">
                    <span class="user-letter">
                        <img src="{{ Auth::user()->profile_picture ? asset('public/storage/' . Auth::user()->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                            alt="Profile Picture" class="img-fluid">
                    </span>
                </span>
            </a>
            <div class="dropdown-menu menu-drop-user">
                <div class="profileset d-flex align-items-center">
                    <span class="user-img me-2">
                        <img src="{{ Auth::user()->profile_picture ? asset('public/storage/' . Auth::user()->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                            alt="Profile Picture">
                    </span>
                    <div>
                        <h6 class="fw-medium">{{ Auth::user()->name }}</h6>
                        <p>{{ Auth::user()->role->name }}</p>
                    </div>
                </div>
                <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="ti ti-user-circle me-2"></i>My
                    Profile</a>
                <hr class="my-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item logout pb-0" type="submit"><i
                            class="ti ti-logout me-2"></i>Logout</button>
                </form>
            </div>
        </li>
    </ul>
    <!-- /Header Menu -->

    <!-- Mobile Menu -->
    <div class="dropdown mobile-user-menu">
        <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
            aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="profile.html">My Profile</a>
            <a class="dropdown-item" href="general-settings.html">Settings</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="dropdown-item logout pb-0" type="submit">Logout</button>
            </form>
        </div>
    </div>
    <!-- /Mobile Menu -->
</div>
