<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Dreams POS is a powerful Bootstrap based Inventory Management Admin Template designed for businesses, offering seamless invoicing, project tracking, and estimates.">
    <meta name="keywords"
        content="inventory management, admin dashboard, bootstrap template, invoicing, estimates, business management, responsive admin, POS system">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} | @yield('title', 'Welcome')</title>

    <script src="{{ asset('assets/js/theme-script.js') }}"></script>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/favicon/site.webmanifest') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">

    <!-- animation CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/feather.css') }}">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Quill CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/quill/quill.snow.css') }}">

    <!-- Bootstrap Tagsinput CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Daterangepikcer CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Map CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jvectormap/jquery-jvectormap-2.0.5.css') }}">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/@simonwep/pickr/themes/nano.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>
        .form-control {
            border-color: #bcc2c7;
        }

        .header .top-nav-search form .form-control {
            border: 1px solid rgb(153 114 114 / 15%) !important;
        }

        .form-select {
            border: 1px solid #bcc2c7;
        }

        .form-check-input {
            border: 1px solid #72818d;
        }

        .delete-button {
            padding: 7px !important;
            background: transparent;
        }

        .delete-button:hover {
            color: #FE9F43;
            background-color: #E6EAED;
        }

        /* Custom styles for the image uploader */
        .image-uploader {
            position: relative;
            width: 150px;
            height: 150px;
            border: 2px dashed #ddd;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            /* Ensures the image preview fits */
            background-color: #f8f9fa;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .image-uploader:hover {
            border-color: #0d6efd;
            background-color: #e9ecef;
        }

        .image-uploader input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            /* Make the default file input invisible */
            cursor: pointer;
        }

        .image-uploader .upload-text {
            text-align: center;
            color: #6c757d;
        }

        .image-uploader .upload-text i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 0.5rem;
        }

        .image-uploader .image-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Crop the image to fit the container */
            position: absolute;
            top: 0;
            left: 0;
        }

        .image-uploader .hover-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            /* Hidden by default */
            transition: opacity 0.2s ease-in-out;
            pointer-events: none;
            /* Allows clicks to pass through to the file input */
        }

        .image-uploader:hover .hover-overlay {
            opacity: 1;
            /* Show on hover */
        }

        .image-uploader .hover-overlay i {
            font-size: 2.5rem;
        }

        /* Class for maintaining a specific aspect ratio */
        .aspect-ratio-box {
            position: relative;
            width: 100%;
            overflow: hidden;
            border-radius: 0.5rem;
            /* Optional: matches your theme's styling */
            background-color: #f8f9fa;
            /* Light background for when images are loading */
        }

        /* Calculate the padding-top percentage: (height / width) * 100 */
        /* For 275x183, the calculation is (183 / 275) * 100 = 66.54% */
        .aspect-ratio-box--275-183::before {
            content: "";
            display: block;
            padding-top: 66.55%;
        }

        .aspect-ratio-box img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* This is the key: it scales and crops the image to fit */
        }

        .variant-btn {
            background-color: #155EEF !important;
            border: 1px solid #155EEF !important;
            box-shadow: 0 3px 10px rgba(21, 94, 239, 0.5);
            color: #ffffff !important;
        }

        .btn-close {
            background-color: transparent !important;
            color: #000 !important;
        }

        /* Header notification animations */
        .notification-message {
            transition: all 0.3s ease;
        }

        .notification-message.new-notification {
            background-color: #f8f9fa;
            border-left: 3px solid #28a745;
            animation: slideInNotification 0.5s ease-out;
        }

        .notification-count-update {
            animation: pulseNotification 0.6s ease-in-out;
        }

        @keyframes slideInNotification {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulseNotification {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    @php
        $unreadNotificationsCount = $unreadNotificationsCount ?? 0;
        $unreadNotifications = $unreadNotifications ?? collect();
    @endphp
    {{-- <div id="global-loader" >
			<div class="whirly-loader"> </div>
		</div>  --}}
    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Header -->
        <div class="header">
            <div class="main-header">

                <!-- Logo -->
                <div class="header-left active">
                    <a href="{{ route('dashboard') }}" class="logo logo-normal">
                        <img src="{{ asset('assets/img/logo.svg') }}" alt="Img">
                    </a>
                    <a href="{{ route('dashboard') }}" class="logo logo-white">
                        <img src="{{ asset('assets/img/logo-white.svg') }}" alt="Img">
                    </a>
                    <a href="{{ route('dashboard') }}" class="logo-small">
                        <img src="{{ asset('assets/img/logo-small.png') }}" alt="Img">
                    </a>
                </div>
                <!-- /Logo -->

                <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>

                <!-- Header Menu -->
                <ul class="nav user-menu">

                    <!-- Search -->
                    <li class="nav-item nav-searchinputs">
                        <div class="top-nav-search">
                            <a href="javascript:void(0);" class="responsive-search">
                                <i class="fa fa-search"></i>
                            </a>
                            <form action="{{ route('global.search') }}" method="GET" class="dropdown"
                                autocomplete="off">
                                <div class="searchinputs input-group dropdown-toggle" id="dropdownMenuClickable"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <input type="text" name="q" placeholder="Search" class="form-control"
                                        autocomplete="off" spellcheck="false" autocorrect="off" autocapitalize="off"
                                        style="padding-left: 25px;">
                                    <div class="search-addon">
                                        <span><i class="ti ti-search"></i></span>
                                    </div>
                                    <span class="input-group-text">
                                        <kbd class="d-flex align-items-center"><img
                                                src="{{ asset('assets/img/icons/command.svg') }}"
                                                alt="img" class="me-1">K</kbd>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </li>
                    <!-- /Search -->
                    @if (auth()->check() &&
                            (optional(auth()->user()->role)->name === 'Super Admin' || optional(auth()->user()->role)->name === 'Sales'))
                        <li class="nav-item pos-nav">
                            <a href="{{ route('pos.index') }}"
                                class="btn btn-dark btn-md d-inline-flex align-items-center">
                                <i class="ti ti-device-laptop me-1"></i>POS
                            </a>
                        </li>
                    @endif
                    <!-- Flag -->
                    {{-- <li class="nav-item dropdown has-arrow flag-nav nav-item-box">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);"
                            role="button">
                            <img src="{{ asset('assets/img/flags/us-flag.svg') }}" alt="Language"
                                class="img-fluid">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="javascript:void(0);" class="dropdown-item">
                                <img src="{{ asset('assets/img/flags/english.svg') }}" alt="Img"
                                    height="16">English
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <img src="{{ asset('assets/img/flags/arabic.svg') }}" alt="Img"
                                    height="16">Arabic
                            </a>
                        </div>
                    </li> --}}
                    <!-- /Flag -->

                    {{-- <li class="nav-item nav-item-box">
                        <a href="javascript:void(0);" id="btnFullscreen">
                            <i class="ti ti-maximize"></i>
                        </a>
                    </li>
                    <li class="nav-item nav-item-box">
                        <a href="email.html">
                            <i class="ti ti-mail"></i>
                            <span class="badge rounded-pill">1</span>
                        </a>
                    </li> --}}
                    <!-- Notifications -->
                    <li class="nav-item dropdown nav-item-box">
                        <a href="javascript:void(0);" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                            <i class="ti ti-bell"></i>
                            {{-- Unread count badge --}}
                            <span class="badge rounded-pill bg-danger" id="notification-count"
                                style="visibility: {{ $unreadNotificationsCount > 0 ? 'visible' : 'hidden' }};">
                                {{ $unreadNotificationsCount }}
                            </span>
                        </a>
                        <div class="dropdown-menu notifications">
                            <div class="topnav-dropdown-header">
                                <h5 class="notification-title">Notifications</h5>
                                <a href="javascript:void(0)" class="clear-noti" id="mark-all-as-read">Mark all as
                                    read</a>
                            </div>
                            <div class="noti-content">
                                <ul class="notification-list" id="notification-list-container">
                                    @forelse ($unreadNotifications as $notification)
                                        <li class="notification-message">
                                            <a href="{{ $notification->data['action_url'] ?? '#' }}">
                                                {{-- Link to the notification action URL --}}
                                                <div class="media d-flex">
                                                    <span class="avatar flex-shrink-0">
                                                        <img alt="Img"
                                                            src="{{ isset($notification->data['sender_avatar']) && $notification->data['sender_avatar'] ? asset($notification->data['sender_avatar']) : asset('storage/images/default_avatar.png') }}">
                                                    </span>
                                                    <div class="flex-grow-1">
                                                        <p class="noti-details">
                                                            <span
                                                                class="noti-title">{{ $notification->data['sender_name'] ?? 'System' }}</span>
                                                            {{ $notification->data['message'] ?? 'New notification' }}
                                                        </p>
                                                        </p>
                                                        <p class="noti-time">
                                                            {{ $notification->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="notification-message" id="no-new-notifications">
                                            <div class="media d-flex justify-content-center">
                                                <p class="text-muted mt-3">No new notifications</p>
                                            </div>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="topnav-dropdown-footer">
                                <a href="#" class="btn btn-primary btn-md w-100">View all</a>
                            </div>
                        </div>
                    </li>
                    <!-- /Notifications -->
                    @auth
                        <li class="nav-item dropdown has-arrow main-drop profile-nav">
                            <a href="javascript:void(0);" class="nav-link userset" data-bs-toggle="dropdown">
                                <span class="user-info p-0">
                                    <span class="user-letter">
                                        <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('storage/images/default_avatar.png') }}"
                                            alt="Profile Picture" class="img-fluid">
                                    </span>
                                </span>
                            </a>
                            <div class="dropdown-menu menu-drop-user">
                                <div class="profileset d-flex align-items-center">
                                    <span class="user-img me-2">
                                        <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('storage/images/default_avatar.png') }}"
                                            alt="Profile Picture">
                                    </span>
                                    <div>
                                        <h6 class="fw-medium">{{ Auth::user()->name }}</h6>
                                        <p>{{ optional(Auth::user()->role)->name }}</p>
                                    </div>
                                </div>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                        class="ti ti-user-circle me-2"></i>My Profile</a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item logout pb-0" type="submit"><i
                                            class="ti ti-logout me-2"></i>Logout</button>
                                </form>
                            </div>
                        </li>
                    @endauth
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
        </div>
        <!-- /Header -->

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <!-- Logo -->
            <div class="sidebar-logo">
                <a href="{{ route('dashboard') }}" class="logo logo-normal">
                    <img src="{{ asset('assets/img/logo.svg') }}" alt="Img">
                </a>
                <a href="{{ route('dashboard') }}" class="logo logo-white">
                    <img src="{{ asset('assets/img/logo-white.svg') }}" alt="Img">
                </a>
                <a href="{{ route('dashboard') }}" class="logo-small">
                    <img src="{{ asset('assets/img/logo-small.png') }}" alt="Img">
                </a>
                <a id="toggle_btn" href="javascript:void(0);">
                    <i data-feather="chevrons-left" class="feather-16"></i>
                </a>
            </div>
            <!-- /Logo -->
            <div class="modern-profile p-3 pb-0">
                <div class="text-center rounded bg-light p-3 mb-4 user-profile">
                    <div class="avatar avatar-lg online mb-3">
                        <img src="{{ asset('assets/img/customer/customer15.jpg') }}" alt="Img"
                            class="img-fluid rounded-circle">
                    </div>
                    <h6 class="fs-14 fw-bold mb-1">Adrian Herman</h6>
                    <p class="fs-12 mb-0">System Admin</p>
                </div>
                <div class="sidebar-nav mb-3">
                    <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent"
                        role="tablist">
                        <li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
                        <li class="nav-item"><a class="nav-link border-0" href="chat.html">Chats</a></li>
                        <li class="nav-item"><a class="nav-link border-0" href="email.html">Inbox</a></li>
                    </ul>
                </div>
            </div>
            <div class="sidebar-header p-3 pb-0 pt-2">
                <div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
                    <div class="avatar avatar-md onlin">
                        <img src="{{ asset('assets/img/customer/customer15.jpg') }}" alt="Img"
                            class="img-fluid rounded-circle">
                    </div>
                    <div class="text-start sidebar-profile-info ms-2">
                        <h6 class="fs-14 fw-bold mb-1">Adrian Herman</h6>
                        <p class="fs-12">System Admin</p>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between menu-item mb-3">
                    <div>
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-icon bg-light">
                            <i class="ti ti-layout-grid-remove"></i>
                        </a>
                    </div>
                    <div>
                        <a href="chat.html" class="btn btn-sm btn-icon bg-light">
                            <i class="ti ti-brand-hipchat"></i>
                        </a>
                    </div>
                    <div>
                        <a href="email.html" class="btn btn-sm btn-icon bg-light position-relative">
                            <i class="ti ti-message"></i>
                        </a>
                    </div>
                    <div class="notification-item">
                        <a href="activities.html" class="btn btn-sm btn-icon bg-light position-relative">
                            <i class="ti ti-bell"></i>
                            <span class="notification-status-dot"></span>
                        </a>
                    </div>
                    <div class="me-0">
                        <a href="general-settings.html" class="btn btn-sm btn-icon bg-light">
                            <i class="ti ti-settings"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        @isset($navItems)
                            {{-- DEFINE ONCE FOR EFFICIENCY --}}
                            @php $currentRouteName = request()->route()->getName(); @endphp

                            @foreach ($navItems->sortBy('order') as $navItem)
                                {{-- Case 1: The item is a HEADER --}}
                                @if ($navItem->type === 'header' && $navItem->children->count() > 0)
                                    <li class="submenu-open">
                                        <h6 class="submenu-hdr">{{ $navItem->name }}</h6>
                                        <ul>
                                            @foreach ($navItem->children->sortBy('order') as $child)
                                                {{-- A) Child is a DROPDOWN --}}
                                                @if ($child->type === 'dropdown' && $child->children->count() > 0)
                                                    @php
                                                        // Check if any grandchild route is active.
                                                        $isSubmenuActive = $child->children
                                                            ->pluck('route')
                                                            ->contains(function ($route) use ($currentRouteName) {
                                                                if (empty($route)) {
                                                                    return false;
                                                                } // Safety check
                                                                $moduleName = explode('.index', $route)[0];
                                                                return \Illuminate\Support\Str::startsWith(
                                                                    $currentRouteName,
                                                                    $moduleName,
                                                                );
                                                            });
                                                    @endphp
                                                    <li class="submenu">
                                                        <a href="javascript:void(0);"
                                                            class="{{ $isSubmenuActive ? 'subdrop active' : '' }}">
                                                            <i
                                                                class="ti {{ $child->icon }} fs-16 me-2"></i><span>{{ $child->name }}</span><span
                                                                class="menu-arrow"></span>
                                                        </a>
                                                        <ul>
                                                            @foreach ($child->children->sortBy('order') as $grandchild)
                                                                @php
                                                                    if (empty($grandchild->route)) {
                                                                        continue;
                                                                    } // Safety check
                                                                    $moduleName = explode(
                                                                        '.index',
                                                                        $grandchild->route,
                                                                    )[0];
                                                                    $isGrandchildActive = \Illuminate\Support\Str::startsWith(
                                                                        $currentRouteName,
                                                                        $moduleName,
                                                                    );
                                                                @endphp
                                                                <li class="{{ $isGrandchildActive ? 'active' : '' }}">
                                                                    <a
                                                                        href="{{ route($grandchild->route) }}">{{ $grandchild->name }}</a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </li>

                                                    {{-- B) Child is a simple LINK --}}
                                                @elseif ($child->type === 'link' && $child->route)
                                                    @php
                                                        // FIX IS HERE: Use $child->route instead of $grandchild->route
                                                        $moduleName = explode('.index', $child->route)[0];
                                                        $isLinkActive = \Illuminate\Support\Str::startsWith(
                                                            $currentRouteName,
                                                            $moduleName,
                                                        );
                                                    @endphp
                                                    <li class="{{ $isLinkActive ? 'active' : '' }}">
                                                        <a href="{{ route($child->route) }}">
                                                            <i
                                                                class="ti {{ $child->icon }} fs-16 me-2"></i><span>{{ $child->name }}</span>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            @endforeach
                        @endisset
                    </ul>
                </div>
            </div>
        </div>
        {{-- <div class="page-wrapper">
            <div class="content">
                <pre>
                @php
                    print_r($navItems->toArray());
                @endphp
                </pre>
            </div>
        </div> --}}
        <!-- /Sidebar -->
        @yield('content')

    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>

    <!-- Feather Icon JS -->
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>

    <!-- Slimscroll JS -->
    <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>

    <!-- Datatable JS -->
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Chart JS -->
    <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexchart/chart-data.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/chart-data.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('assets/plugins/peity/jquery.peity.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/peity/chart-data.js') }}"></script>

    <!-- Daterangepikcer JS -->
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>

    <!-- Bootstrap Tagsinput JS -->
    <script src="{{ asset('assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>

    <!-- Color Picker JS -->
    <script src="{{ asset('assets/plugins/@simonwep/pickr/pickr.es5.min.js') }}"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Quill JS -->
    <script src="{{ asset('assets/plugins/quill/quill.min.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert/sweetalerts.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/theme-colorpicker.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>

    {{-- 1. Load the main compiled app.js that DEFINES window.Echo --}}
    @vite('resources/js/app.js')

    {{-- 2. Include our listener script that USES window.Echo --}}
    @include('layouts.partials._echo-script')

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            // Mark all notifications as read functionality
            $('#mark-all-as-read').on('click', function(e) {
                e.preventDefault();

                // Use global function to sync all notification displays
                if (typeof window.markAllNotificationsAsReadGlobally === 'function') {
                    window.markAllNotificationsAsReadGlobally().then(success => {
                        if (!success) {
                            alert('Could not mark notifications as read.');
                        }
                    });
                } else {
                    // Fallback to old method
                    $.ajax({
                        url: '{{ route('notifications.markAsRead') }}',
                        type: 'POST',
                        success: function(response) {
                            if (response.success) {
                                // Reset notification count badge
                                $('#notification-count').text('0').css('visibility', 'hidden');

                                // Show no notifications message
                                $('#notification-list-container').html(`
                                    <li class="notification-message" id="no-new-notifications">
                                        <div class="media d-flex justify-content-center">
                                            <p class="text-muted mt-3">No new notifications</p>
                                        </div>
                                    </li>
                                `);
                            }
                        },
                        error: function() {
                            alert('Could not mark notifications as read.');
                        }
                    });
                }
            });

            // keyboard shortcut: focus the header search input with 'k' or '/'
            $(document).on('keydown', function(e) {
                const tag = e.target.tagName.toLowerCase();
                if (tag === 'input' || tag === 'textarea') return;
                if (e.key === 'k' || e.key === '/') {
                    e.preventDefault();
                    $('.searchinputs input').first().focus();
                }
            });

            // Open Recent Searches dropdown only when the responsive search icon or addon is clicked.
            // Do NOT open on input focus to avoid showing recent searches unintentionally.
            $('.top-nav-search').on('click', '.responsive-search, .search-addon, .search-addon *', function(e) {
                try {
                    // find the .searchinputs container in this top-nav-search
                    const toggleEl = $(this).closest('.top-nav-search').find('.searchinputs')[0];
                    if (toggleEl) {
                        var dd = bootstrap.Dropdown.getOrCreateInstance(toggleEl);
                        dd.show();
                    }
                } catch (err) {
                    // fail silently
                    console.error('Could not open search dropdown:', err);
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
