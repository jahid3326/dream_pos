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
    <title>{{ config('app.name') }} | @yield('title', 'Welcome')</title>

    <script src="{{ asset('public/assets/js/theme-script.js') }}"></script>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('public/assets/img/favicon.png') }}">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/assets/img/apple-touch-icon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap-datetimepicker.min.css') }}">

    <!-- animation CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/animate.css') }}">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/feather.css') }}">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/select2/css/select2.min.css') }}">

    <!-- Quill CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/quill/quill.snow.css') }}">

    <!-- Bootstrap Tagsinput CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.min.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/fontawesome/css/all.min.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Daterangepikcer CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Map CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/jvectormap/jquery-jvectormap-2.0.5.css') }}">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/@simonwep/pickr/themes/nano.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">

    <style>
        .form-control {
            border-color: #bcc2c7;
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
    </style>

</head>

<body>
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
                    <a href="index.html" class="logo logo-normal">
                        <img src="{{ asset('public/assets/img/logo.svg') }}" alt="Img">
                    </a>
                    <a href="index.html" class="logo logo-white">
                        <img src="{{ asset('public/assets/img/logo-white.svg') }}" alt="Img">
                    </a>
                    <a href="index.html" class="logo-small">
                        <img src="{{ asset('public/assets/img/logo-small.png') }}" alt="Img">
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
                            <form action="#" class="dropdown">
                                <div class="searchinputs input-group dropdown-toggle" id="dropdownMenuClickable"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <input type="text" placeholder="Search">
                                    <div class="search-addon">
                                        <span><i class="ti ti-search"></i></span>
                                    </div>
                                    <span class="input-group-text">
                                        <kbd class="d-flex align-items-center"><img
                                                src="{{ asset('public/assets/img/icons/command.svg') }}" alt="img"
                                                class="me-1">K</kbd>
                                    </span>
                                </div>
                                <div class="dropdown-menu search-dropdown" aria-labelledby="dropdownMenuClickable">
                                    <div class="search-info">
                                        <h6><span><i data-feather="search" class="feather-16"></i></span>Recent Searches
                                        </h6>
                                        <ul class="search-tags">
                                            <li><a href="javascript:void(0);">Products</a></li>
                                            <li><a href="javascript:void(0);">Sales</a></li>
                                            <li><a href="javascript:void(0);">Applications</a></li>
                                        </ul>
                                    </div>
                                    <div class="search-info">
                                        <h6><span><i data-feather="help-circle" class="feather-16"></i></span>Help
                                        </h6>
                                        <p>How to Change Product Volume from 0 to 200 on Inventory management</p>
                                        <p>Change Product Name</p>
                                    </div>
                                    <div class="search-info">
                                        <h6><span><i data-feather="user" class="feather-16"></i></span>Customers</h6>
                                        <ul class="customers">
                                            <li><a href="javascript:void(0);">Aron Varu<img
                                                        src="{{ asset('public/assets/img/profiles/avator1.jpg') }}"
                                                        alt="Img" class="img-fluid"></a></li>
                                            <li><a href="javascript:void(0);">Jonita<img
                                                        src="{{ asset('public/assets/img/profiles/avatar-01.jpg') }}"
                                                        alt="Img" class="img-fluid"></a></li>
                                            <li><a href="javascript:void(0);">Aaron<img
                                                        src="{{ asset('public/assets/img/profiles/avatar-10.jpg') }}"
                                                        alt="Img" class="img-fluid"></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </li>
                    <!-- /Search -->

                    <!-- Select Store -->
                    <li class="nav-item dropdown has-arrow main-drop select-store-dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle nav-link select-store"
                            data-bs-toggle="dropdown">
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

                    <li class="nav-item dropdown link-nav">
                        <a href="javascript:void(0);" class="btn btn-primary btn-md d-inline-flex align-items-center"
                            data-bs-toggle="dropdown">
                            <i class="ti ti-circle-plus me-1"></i>Add New
                        </a>
                        <div class="dropdown-menu dropdown-xl dropdown-menu-center">
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <a href="category-list.html" class="link-item">
                                        <span class="link-icon">
                                            <i class="ti ti-brand-codepen"></i>
                                        </span>
                                        <p>Category</p>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="add-product.html" class="link-item">
                                        <span class="link-icon">
                                            <i class="ti ti-square-plus"></i>
                                        </span>
                                        <p>Product</p>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="category-list.html" class="link-item">
                                        <span class="link-icon">
                                            <i class="ti ti-shopping-bag"></i>
                                        </span>
                                        <p>Purchase</p>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="online-orders.html" class="link-item">
                                        <span class="link-icon">
                                            <i class="ti ti-shopping-cart"></i>
                                        </span>
                                        <p>Sale</p>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="expense-list.html" class="link-item">
                                        <span class="link-icon">
                                            <i class="ti ti-file-text"></i>
                                        </span>
                                        <p>Expense</p>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="quotation-list.html" class="link-item">
                                        <span class="link-icon">
                                            <i class="ti ti-device-floppy"></i>
                                        </span>
                                        <p>Quotation</p>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="sales-returns.html" class="link-item">
                                        <span class="link-icon">
                                            <i class="ti ti-copy"></i>
                                        </span>
                                        <p>Return</p>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="users.html" class="link-item">
                                        <span class="link-icon">
                                            <i class="ti ti-user"></i>
                                        </span>
                                        <p>User</p>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="customers.html" class="link-item">
                                        <span class="link-icon">
                                            <i class="ti ti-users"></i>
                                        </span>
                                        <p>Customer</p>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="sales-report.html" class="link-item">
                                        <span class="link-icon">
                                            <i class="ti ti-shield"></i>
                                        </span>
                                        <p>Biller</p>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="suppliers.html" class="link-item">
                                        <span class="link-icon">
                                            <i class="ti ti-user-check"></i>
                                        </span>
                                        <p>Supplier</p>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <a href="stock-transfer.html" class="link-item">
                                        <span class="link-icon">
                                            <i class="ti ti-truck"></i>
                                        </span>
                                        <p>Transfer</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item pos-nav">
                        <a href="pos.html" class="btn btn-dark btn-md d-inline-flex align-items-center">
                            <i class="ti ti-device-laptop me-1"></i>POS
                        </a>
                    </li>

                    <!-- Flag -->
                    <li class="nav-item dropdown has-arrow flag-nav nav-item-box">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);"
                            role="button">
                            <img src="{{ asset('public/assets/img/flags/us-flag.svg') }}" alt="Language"
                                class="img-fluid">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="javascript:void(0);" class="dropdown-item">
                                <img src="{{ asset('public/assets/img/flags/english.svg') }}" alt="Img"
                                    height="16">English
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <img src="{{ asset('public/assets/img/flags/arabic.svg') }}" alt="Img"
                                    height="16">Arabic
                            </a>
                        </div>
                    </li>
                    <!-- /Flag -->

                    <li class="nav-item nav-item-box">
                        <a href="javascript:void(0);" id="btnFullscreen">
                            <i class="ti ti-maximize"></i>
                        </a>
                    </li>
                    <li class="nav-item nav-item-box">
                        <a href="email.html">
                            <i class="ti ti-mail"></i>
                            <span class="badge rounded-pill">1</span>
                        </a>
                    </li>
                    <!-- Notifications -->
                    <li class="nav-item dropdown nav-item-box">
                        <a href="javascript:void(0);" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                            <i class="ti ti-bell"></i>
                        </a>
                        <div class="dropdown-menu notifications">
                            <div class="topnav-dropdown-header">
                                <h5 class="notification-title">Notifications</h5>
                                <a href="javascript:void(0)" class="clear-noti">Mark all as read</a>
                            </div>
                            <div class="noti-content">
                                <ul class="notification-list">
                                    <li class="notification-message">
                                        <a href="activities.html">
                                            <div class="media d-flex">
                                                <span class="avatar flex-shrink-0">
                                                    <img alt="Img"
                                                        src="{{ asset('public/assets/img/profiles/avatar-13.jpg') }}">
                                                </span>
                                                <div class="flex-grow-1">
                                                    <p class="noti-details"><span class="noti-title">James
                                                            Kirwin</span> confirmed his order. Order No:
                                                        #78901.Estimated delivery: 2 days</p>
                                                    <p class="noti-time">4 mins ago</p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="notification-message">
                                        <a href="activities.html">
                                            <div class="media d-flex">
                                                <span class="avatar flex-shrink-0">
                                                    <img alt="Img"
                                                        src="{{ asset('public/assets/img/profiles/avatar-03.jpg') }}">
                                                </span>
                                                <div class="flex-grow-1">
                                                    <p class="noti-details"><span class="noti-title">Leo Kelly</span>
                                                        cancelled his order scheduled for 17 Jan 2025</p>
                                                    <p class="noti-time">10 mins ago</p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="notification-message">
                                        <a href="activities.html" class="recent-msg">
                                            <div class="media d-flex">
                                                <span class="avatar flex-shrink-0">
                                                    <img alt="Img"
                                                        src="{{ asset('public/assets/img/profiles/avatar-17.jpg') }}">
                                                </span>
                                                <div class="flex-grow-1">
                                                    <p class="noti-details">Payment of $50 received for Order #67890
                                                        from <span class="noti-title">Antonio Engle</span></p>
                                                    <p class="noti-time">05 mins ago</p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="notification-message">
                                        <a href="activities.html" class="recent-msg">
                                            <div class="media d-flex">
                                                <span class="avatar flex-shrink-0">
                                                    <img alt="Img"
                                                        src="{{ asset('public/assets/img/profiles/avatar-02.jpg') }}">
                                                </span>
                                                <div class="flex-grow-1">
                                                    <p class="noti-details"><span class="noti-title">Andrea</span>
                                                        confirmed his order. Order No: #73401.Estimated delivery: 3 days
                                                    </p>
                                                    <p class="noti-time">4 mins ago</p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="topnav-dropdown-footer d-flex align-items-center gap-3">
                                <a href="#" class="btn btn-secondary btn-md w-100">Cancel</a>
                                <a href="activities.html" class="btn btn-primary btn-md w-100">View all</a>
                            </div>
                        </div>
                    </li>
                    <!-- /Notifications -->

                    <li class="nav-item nav-item-box">
                        <a href="general-settings.html"><i class="ti ti-settings"></i></a>
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
                            <a class="dropdown-item" href="profile.html"><i
                                    class="ti ti-user-circle me-2"></i>MyProfile</a>
                            <a class="dropdown-item" href="sales-report.html"><i
                                    class="ti ti-file-text me-2"></i>Reports</a>
                            <a class="dropdown-item" href="general-settings.html"><i
                                    class="ti ti-settings-2 me-2"></i>Settings</a>
                            <hr class="my-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item logout pb-0" type="submit"><i
                                        class="ti ti-logout me-2"></i>Logout</button>
                            </form>
                            {{-- <a class="dropdown-item logout pb-0" href="signin.html"><i
                                    class="ti ti-logout me-2"></i>Logout</a> --}}
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
                        <a class="dropdown-item" href="signin.html">Logout</a>
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
                <a href="index.html" class="logo logo-normal">
                    <img src="{{ asset('public/assets/img/logo.svg') }}" alt="Img">
                </a>
                <a href="index.html" class="logo logo-white">
                    <img src="{{ asset('public/assets/img/logo-white.svg') }}" alt="Img">
                </a>
                <a href="index.html" class="logo-small">
                    <img src="{{ asset('public/assets/img/logo-small.png') }}" alt="Img">
                </a>
                <a id="toggle_btn" href="javascript:void(0);">
                    <i data-feather="chevrons-left" class="feather-16"></i>
                </a>
            </div>
            <!-- /Logo -->
            <div class="modern-profile p-3 pb-0">
                <div class="text-center rounded bg-light p-3 mb-4 user-profile">
                    <div class="avatar avatar-lg online mb-3">
                        <img src="{{ asset('public/assets/img/customer/customer15.jpg') }}" alt="Img"
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
                        <img src="{{ asset('public/assets/img/customer/customer15.jpg') }}" alt="Img"
                            class="img-fluid rounded-circle">
                    </div>
                    <div class="text-start sidebar-profile-info ms-2">
                        <h6 class="fs-14 fw-bold mb-1">Adrian Herman</h6>
                        <p class="fs-12">System Admin</p>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between menu-item mb-3">
                    <div>
                        <a href="index.html" class="btn btn-sm btn-icon bg-light">
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
        <!-- /Sidebar -->
        @yield('content')

    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('public/assets/js/jquery-3.7.1.min.js') }}"></script>

    <!-- Feather Icon JS -->
    <script src="{{ asset('public/assets/js/feather.min.js') }}"></script>

    <!-- Slimscroll JS -->
    <script src="{{ asset('public/assets/js/jquery.slimscroll.min.js') }}"></script>

    <!-- Datatable JS -->
    <script src="{{ asset('public/assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Chart JS -->
    <script src="{{ asset('public/assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('public/assets/plugins/apexchart/chart-data.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('public/assets/plugins/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('public/assets/plugins/chartjs/chart-data.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('public/assets/plugins/peity/jquery.peity.min.js') }}"></script>
    <script src="{{ asset('public/assets/plugins/peity/chart-data.js') }}"></script>

    <!-- Daterangepikcer JS -->
    <script src="{{ asset('public/assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('public/assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('public/assets/js/bootstrap-datetimepicker.min.js') }}"></script>

    <!-- Bootstrap Tagsinput JS -->
    <script src="{{ asset('public/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>

    <!-- Color Picker JS -->
    <script src="{{ asset('public/assets/plugins/@simonwep/pickr/pickr.es5.min.js') }}"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('public/assets/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Quill JS -->
    <script src="{{ asset('public/assets/plugins/quill/quill.min.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('public/assets/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('public/assets/plugins/sweetalert/sweetalerts.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('public/assets/js/theme-colorpicker.js') }}"></script>
    <script src="{{ asset('public/assets/js/script.js') }}"></script>

    @stack('scripts')
</body>

</html>
