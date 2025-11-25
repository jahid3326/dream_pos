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
            <span class="bg-teal text-white d-inline-flex align-items-center"><img
                    src="{{ asset('public/assets/img/icons/clock-icon.svg') }}" alt="img"
                    class="me-2">09:25:32</span>
        </li>
        <!-- /Search -->

        <li class="nav-item pos-nav">
            <a href="{{ route('dashboard') }}" class="btn btn-purple btn-md d-inline-flex align-items-center">
                <i class="ti ti-world me-1"></i>Dashboard
            </a>
        </li>

        <!-- Select Store -->
        <li class="nav-item dropdown has-arrow main-drop select-store-dropdown">
            <a href="javascript:void(0);" class="dropdown-toggle nav-link select-store" data-bs-toggle="dropdown">
                <span class="user-info">
                    <span class="user-letter">
                        <img src="{{ asset('public/assets/img/store/store-01.png') }}" alt="Store Logo"
                            class="img-fluid">
                    </span>
                    <span class="user-detail">
                        <span class="user-name">Freshmart</span>
                    </span>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="javascript:void(0);" class="dropdown-item">
                    <img src="{{ asset('public/assets/img/store/store-01.png') }}" alt="Store Logo"
                        class="img-fluid">Freshmart
                </a>
                <a href="javascript:void(0);" class="dropdown-item">
                    <img src="{{ asset('public/assets/img/store/store-02.png') }}" alt="Store Logo"
                        class="img-fluid">Grocery Apex
                </a>
                <a href="javascript:void(0);" class="dropdown-item">
                    <img src="{{ asset('public/assets/img/store/store-03.png') }}" alt="Store Logo"
                        class="img-fluid">Grocery Bevy
                </a>
                <a href="javascript:void(0);" class="dropdown-item">
                    <img src="{{ asset('public/assets/img/store/store-04.png') }}" alt="Store Logo"
                        class="img-fluid">Grocery Eden
                </a>
            </div>
        </li>
        <!-- /Select Store -->

        <li class="nav-item nav-item-box">
            <a href="#" data-bs-toggle="modal" data-bs-target="#calculator"
                class="bg-orange border-orange text-white"><i class="ti ti-calculator"></i></a>
        </li>
        <li class="nav-item nav-item-box">
            <a href="javascript:void(0);" id="btnFullscreen" data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Maximize">
                <i class="ti ti-maximize"></i>
            </a>
        </li>
        <li class="nav-item nav-item-box" data-bs-toggle="tooltip" data-bs-placement="top"
            data-bs-title="Cash Register">
            <a href="#" data-bs-toggle="modal" data-bs-target="#cash-register"><i class="ti ti-cash"></i></a>
        </li>
        <li class="nav-item nav-item-box" data-bs-toggle="tooltip" data-bs-placement="top"
            data-bs-title="Print Last Reciept">
            <a href="#"><i class="ti ti-printer"></i></a>
        </li>
        <li class="nav-item nav-item-box" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Today’s Sale">
            <a href="#" data-bs-toggle="modal" data-bs-target="#today-sale"><i class="ti ti-progress"></i></a>
        </li>
        <li class="nav-item nav-item-box" data-bs-toggle="tooltip" data-bs-placement="top"
            data-bs-title="Today’s Profit">
            <a href="#" data-bs-toggle="modal" data-bs-target="#today-profit"><i
                    class="ti ti-chart-infographic"></i></a>
        </li>
        <li class="nav-item nav-item-box" data-bs-toggle="tooltip" data-bs-placement="top"
            data-bs-title="POS Settings">
            <a href="pos-settings.html"><i class="ti ti-settings"></i></a>
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
                <a class="dropdown-item" href="profile.html"><i class="ti ti-user-circle me-2"></i>MyProfile</a>
                <a class="dropdown-item" href="sales-report.html"><i class="ti ti-file-text me-2"></i>Reports</a>
                <a class="dropdown-item" href="general-settings.html"><i
                        class="ti ti-settings-2 me-2"></i>Settings</a>
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
