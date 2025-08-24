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
    <title>{{ config('app.name') }} | POS</title>

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

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/select2/css/select2.min.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.min.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/fontawesome/css/all.min.css') }}">

    <!-- Daterangepikcer CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap-datetimepicker.min.css') }}">

    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/owlcarousel/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/owlcarousel/owl.theme.default.min.css') }}">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/@simonwep/pickr/themes/nano.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">

    <style>
        .product-image {
            width: 197px !important;
            height: 131px !important;
        }
    </style>
</head>

<body class="pos-page">
    <!-- Main Wrapper -->
    <div class="main-wrapper pos-three pos-four">

        <!-- Header -->
        <div class="header pos-header">

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

                <li class="nav-item nav-item-box">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#calculator"
                        class="bg-orange border-orange text-white"><i class="ti ti-calculator"></i></a>
                </li>
                <li class="nav-item nav-item-box">
                    <a href="javascript:void(0);" id="btnFullscreen" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="Maximize">
                        <i class="ti ti-maximize"></i>
                    </a>
                </li>
                <li class="nav-item nav-item-box" data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-title="Cash Register">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#cash-register"><i
                            class="ti ti-cash"></i></a>
                </li>
                <li class="nav-item nav-item-box" data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-title="Print Last Reciept">
                    <a href="#"><i class="ti ti-printer"></i></a>
                </li>
                <li class="nav-item nav-item-box" data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-title="Today’s Sale">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#today-sale"><i
                            class="ti ti-progress"></i></a>
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
        <!-- Header -->

        <div class="page-wrapper pos-pg-wrapper ms-0">
            <div class="content pos-design p-0">

                <div class="row align-items-start pos-wrapper">

                    <!-- Products -->
                    <div class="col-md-12 col-lg-6">
                        <div class="pos-categories tabs_wrapper">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                                <div>
                                    <h5 class="mb-1">Welcome, {{ Auth::user()->name }}</h5>
                                    <p>{{ now()->format('F d, Y') }}</p>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    <div class="input-icon-start pos-search position-relative">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-search"></i>
                                        </span>
                                        <input type="text" class="form-control" placeholder="Search Product">
                                    </div>
                                    <a href="#" class="btn btn-sm btn-primary">View All Categories</a>
                                </div>
                            </div>
                            <ul class="tabs owl-carousel pos-category4 mb-4">
                                <li id="all" class="active">
                                    <h6><a href="javascript:void(0);">All Categories</a></h6>
                                </li>

                                {{-- A simple loop for the flat list of child categories --}}
                                @foreach ($categories as $category)
                                    <li id="cat-{{ $category->id }}">
                                        <h6><a href="javascript:void(0);">{{ $category->name }}</a></h6>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="pos-products">
                                <div class="tabs_container">
                                    <div class="tab_content active" data-tab="all">
                                        <div class="row g-3">
                                            {{-- DYNAMIC PRODUCT GRID --}}
                                            @foreach ($products as $product)
                                                <div class="col-sm-6 col-md-4 col-lg-6 col-xl-4 col-xxl-3 product-item"
                                                    data-category-id="{{ $product['category_id'] }}">
                                                    <div class="product-info card"
                                                        data-product-id="{{ $product['id'] }}"
                                                        data-variation-id="{{ $product['variation_id'] }}"
                                                        data-name="{{ $product['name'] }}"
                                                        data-price="{{ $product['price'] }}"
                                                        data-tax-rate="{{ $product['tax_rate'] }}">
                                                        <a href="javascript:void(0);" class="product-image">
                                                            <img src="{{ $product['image'] }}" alt="Product Image"
                                                                class="w-100">
                                                        </a>
                                                        <div class="product-content text-center">
                                                            <h6 class="fs-14 fw-bold mb-1"><a
                                                                    href="javascript:void(0);">{{ $product['name'] }}</a>
                                                            </h6>
                                                            <div class="text-center">
                                                                <span
                                                                    class="fs-14 fw-semibold text-gray-6">${{ number_format($product['price'], 2) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Products -->

                    <!-- Order Details -->
                    <div class="col-md-12 col-lg-6 ps-0 theiaStickySidebar">
                        <aside class="product-order-list">
                            <div class="customer-info">
                                <div
                                    class="order-head bg-light d-flex align-items-center justify-content-between w-100 mb-3">
                                    <div>
                                        <h3>Order List</h3>
                                        <span>Transaction ID : #65565</span>
                                    </div>
                                    <div>
                                        <a class="link-danger fs-16" href="javascript:void(0);"><i
                                                class="ti ti-trash-x-filled"></i></a>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="input-icon-end position-relative">
                                            <input type="text" class="form-control datetimepicker"
                                                placeholder="dd/mm/yyyy">
                                            <span class="input-icon-addon">
                                                <i class="ti ti-calendar text-gray-7"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" placeholder="Type Ref Number">
                                    </div>
                                    <div class="col-md-4">
                                        <select class="select">
                                            <option>Search Shop</option>
                                            <option>IPhone 14 64GB</option>
                                            <option>MacBook Pro</option>
                                            <option>Rolex Tribute V3</option>
                                            <option>Red Nike Angelo</option>
                                            <option>Airpod 2</option>
                                            <option>Oldest</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="w-100">
                                                <select class="select" name="customer_id">
                                                    <option value="">Walk-in Customer</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}">
                                                            {{ $customer->user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <a href="#" class="btn btn-primary btn-icon" data-bs-toggle="modal"
                                                data-bs-target="#create"><i class="ti ti-user-plus"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="product-added block-section">
                                <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                                    <h5 class="d-flex align-items-center mb-0">Order Details</h5>
                                    <div class="badge bg-light text-gray-9 fs-12 fw-semibold py-2 border rounded">
                                        Items : <span class="text-teal">3</span></div>
                                </div>
                                <div class="product-wrap">
                                    <div class="empty-cart text-center">
                                        <div class="mb-1"><img
                                                src="{{ asset('public/assets/img/icons/empty-cart.svg') }}"
                                                alt="img"></div>
                                        <p class="fw-bold">No Products Selected</p>
                                    </div>
                                    <div class="table-responsive" style="display: none;">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th class="bg-transparent fw-bold">Product</th>
                                                    <th class="bg-transparent fw-bold">QTY</th>
                                                    <th class="bg-transparent fw-bold">Price</th>
                                                    <th class="bg-transparent fw-bold">Sub Total</th>
                                                    <th class="bg-transparent fw-bold text-end"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="pos-cart-body"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="block-section order-method bg-light m-0">
                                <div class="order-total">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td>Sub Total</td>
                                                    <td class="text-end" id="cart-subtotal">$0.00</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Shipping
                                                        {{-- This link can open a modal to set the shipping cost --}}
                                                        <a href="#" class="ms-3 link-default"
                                                            data-bs-toggle="modal" data-bs-target="#shipping-cost">
                                                            <i class="ti ti-edit"></i>
                                                        </a>
                                                        {{-- Hidden input to store the shipping value --}}
                                                        <input type="hidden" id="shipping-value" value="0">
                                                    </td>
                                                    <td class="text-end" id="cart-shipping">$0.00</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Tax (<span id="tax-name-display">None</span>)
                                                        <a href="#" class="ms-3 link-default"
                                                            data-bs-toggle="modal" data-bs-target="#order-tax">
                                                            <i class="ti ti-edit"></i>
                                                        </a>
                                                        {{-- Hidden input to store the selected tax rate for calculations --}}
                                                        <input type="hidden" id="order-tax-rate" value="0">
                                                    </td>
                                                    <td class="text-end" id="cart-tax">$0.00</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="text-danger">Discount</span>
                                                        <a href="#" class="ms-3 link-default"
                                                            data-bs-toggle="modal" data-bs-target="#discount">
                                                            <i class="ti ti-edit"></i>
                                                        </a>
                                                        <input type="hidden" id="discount-value" value="0">
                                                    </td>
                                                    <td class="text-danger text-end" id="cart-discount">-$0.00</td>
                                                </tr>
                                                <tr class="total-row">
                                                    <td class="fw-bold">Grand Total</td>
                                                    <td class="text-end fw-bold" id="cart-grandtotal">$0.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row gx-2">
                                    <div class="col-sm-4">
                                        <a href="javascript:void(0);"
                                            class="btn btn-orange d-flex align-items-center justify-content-center w-100 mb-2"
                                            data-bs-toggle="modal" data-bs-target="#hold-order"><i
                                                class="ti ti-player-pause me-2"></i>Hold</a>
                                        <a href="javascript:void(0);"
                                            class="btn btn-secondary d-flex align-items-center justify-content-center w-100 mb-2"
                                            data-bs-toggle="modal" data-bs-target="#orders"><i
                                                class="ti ti-shopping-cart me-2"></i>View Orders</a>
                                    </div>
                                    <div class="col-sm-4">
                                        <a href="javascript:void(0);"
                                            class="btn btn-info d-flex align-items-center justify-content-center w-100 mb-2"><i
                                                class="ti ti-trash me-2"></i>Void</a>
                                        <a href="javascript:void(0);"
                                            class="btn btn-indigo d-flex align-items-center justify-content-center w-100 mb-2"
                                            data-bs-toggle="modal" data-bs-target="#reset"><i
                                                class="ti ti-reload me-2"></i>Reset</a>
                                    </div>
                                    <div class="col-sm-4">
                                        <a href="javascript:void(0);"
                                            class="btn btn-cyan d-flex align-items-center justify-content-center w-100 mb-2"
                                            data-bs-toggle="modal" data-bs-target="#payment-completed"><i
                                                class="ti ti-cash-banknote me-2"></i>Payment</a>
                                        <a href="javascript:void(0);"
                                            class="btn btn-danger d-flex align-items-center justify-content-center w-100 mb-2"
                                            data-bs-toggle="modal" data-bs-target="#recents"><i
                                                class="ti ti-refresh-dot me-2"></i>Transaction</a>
                                    </div>
                                </div>
                            </div>
                            <div class="block-section payment-method">
                                <h5 class="mb-2">Select Payment</h5>
                                <div class="row align-items-center justify-content-center methods g-2 mb-4">
                                    <div class="col-sm-6 col-md-4 col-xl d-flex">
                                        <a href="javascript:void(0);" class="payment-item flex-fill"
                                            data-bs-toggle="modal" data-bs-target="#payment-cash">
                                            <img src="{{ asset('public/assets/img/icons/cash-icon.svg') }}"
                                                alt="img">
                                            <p class="fw-medium">Cash</p>
                                        </a>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-xl d-flex">
                                        <a href="javascript:void(0);" class="payment-item flex-fill"
                                            data-bs-toggle="modal" data-bs-target="#payment-card">
                                            <img src="{{ asset('public/assets/img/icons/card.svg') }}"
                                                alt="img">
                                            <p class="fw-medium">Card</p>
                                        </a>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-xl d-flex">
                                        <a href="javascript:void(0);" class="payment-item flex-fill"
                                            data-bs-toggle="modal" data-bs-target="#payment-points">
                                            <img src="{{ asset('public/assets/img/icons/points.svg') }}"
                                                alt="img">
                                            <p class="fw-medium">Points</p>
                                        </a>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-xl d-flex">
                                        <a href="javascript:void(0);" class="payment-item flex-fill"
                                            data-bs-toggle="modal" data-bs-target="#payment-deposit">
                                            <img src="{{ asset('public/assets/img/icons/deposit.svg') }}"
                                                alt="img">
                                            <p class="fw-medium">Deposit</p>
                                        </a>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-xl d-flex">
                                        <a href="javascript:void(0);" class="payment-item flex-fill"
                                            data-bs-toggle="modal" data-bs-target="#payment-cheque">
                                            <img src="{{ asset('public/assets/img/icons/cheque.svg') }}"
                                                alt="img">
                                            <p class="fw-medium">Cheque</p>
                                        </a>
                                    </div>
                                </div>
                                <div class="btn-block m-0">
                                    <a class="btn btn-teal w-100" href="javascript:void(0);">
                                        Pay : $56590.00
                                    </a>
                                </div>
                            </div>
                        </aside>
                    </div>
                    <!-- /Order Details -->

                </div>
            </div>
        </div>

    </div>
    <!-- /Main Wrapper -->

    <!-- Payment Completed -->
    <div class="modal fade modal-default" id="payment-completed" aria-labelledby="payment-completed">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="success-wrap text-center">
                        <form action="pos-5.html">
                            <div class="icon-success bg-success text-white mb-2">
                                <i class="ti ti-check"></i>
                            </div>
                            <h3 class="mb-2">Payment Completed</h3>
                            <p class="mb-3">Do you want to Print Receipt for the Completed Order</p>
                            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                <button type="button" class="btn btn-md btn-secondary" data-bs-toggle="modal"
                                    data-bs-target="#print-receipt">Print Receipt<i
                                        class="feather-arrow-right-circle icon-me-5"></i></button>
                                <button type="submit" class="btn btn-md btn-primary">Next Order</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Payment Completed -->

    <!-- Print Receipt -->
    <div class="modal fade modal-default" id="print-receipt" aria-labelledby="print-receipt">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="icon-head text-center">
                        <a href="javascript:void(0);">
                            <img src="{{ asset('public/assets/img/logo.svg') }}" width="100" height="30"
                                alt="Receipt Logo">
                        </a>
                    </div>
                    <div class="text-center info text-center">
                        <h6>Dreamguys Technologies Pvt Ltd.,</h6>
                        <p class="mb-0">Phone Number: +1 5656665656</p>
                        <p class="mb-0">Email: <a href="mailto:example@gmail.com">example@gmail.com</a></p>
                    </div>
                    <div class="tax-invoice">
                        <h6 class="text-center">Tax Invoice</h6>
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="invoice-user-name"><span>Name: </span>John Doe</div>
                                <div class="invoice-user-name"><span>Invoice No: </span>CS132453</div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="invoice-user-name"><span>Customer Id: </span>#LL93784</div>
                                <div class="invoice-user-name"><span>Date: </span>01.07.2022</div>
                            </div>
                        </div>
                    </div>
                    <table class="table-borderless w-100 table-fit">
                        <thead>
                            <tr>
                                <th># Item</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1. Red Nike Laser</td>
                                <td>$50</td>
                                <td>3</td>
                                <td class="text-end">$150</td>
                            </tr>
                            <tr>
                                <td>2. Iphone 14</td>
                                <td>$50</td>
                                <td>2</td>
                                <td class="text-end">$100</td>
                            </tr>
                            <tr>
                                <td>3. Apple Series 8</td>
                                <td>$50</td>
                                <td>3</td>
                                <td class="text-end">$150</td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <table class="table-borderless w-100 table-fit">
                                        <tr>
                                            <td class="fw-bold">Sub Total :</td>
                                            <td class="text-end">$700.00</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Discount :</td>
                                            <td class="text-end">-$50.00</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Shipping :</td>
                                            <td class="text-end">0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Tax (5%) :</td>
                                            <td class="text-end">$5.00</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Total Bill :</td>
                                            <td class="text-end">$655.00</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Due :</td>
                                            <td class="text-end">$0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Total Payable :</td>
                                            <td class="text-end">$655.00</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-center invoice-bar">
                        <div class="border-bottom border-dashed">
                            <p>**VAT against this challan is payable through central registration. Thank you for your
                                business!</p>
                        </div>
                        <a href="javascript:void(0);">
                            <img src="{{ asset('public/assets/img/barcode/barcode-03.jpg') }}" alt="Barcode">
                        </a>
                        <p class="text-dark fw-bold">Sale 31</p>
                        <p>Thank You For Shopping With Us. Please Come Again</p>
                        <a href="javascript:void(0);" class="btn btn-md btn-primary">Print Receipt</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Print Receipt -->

    <!-- Products -->
    <div class="modal fade modal-default pos-modal" id="products" aria-labelledby="products">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h5 class="me-4">Products</h5>
                    </div>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                                <span class="badge bg-dark fs-12">Order ID : #45698</span>
                                <p class="fs-16">Number of Products : 02</p>
                            </div>
                            <div class="product-wrap h-auto">
                                <div class="product-list bg-white align-items-center justify-content-between">
                                    <div class="d-flex align-items-center product-info" data-bs-toggle="modal"
                                        data-bs-target="#products">
                                        <a href="javascript:void(0);" class="img-bg">
                                            <img src="{{ asset('public/assets/img/products/pos-product-16.png') }}"
                                                alt="Products">
                                        </a>
                                        <div class="info">
                                            <h6><a href="javascript:void(0);">Red Nike Laser</a></h6>
                                            <p>Quantity : 04</p>
                                        </div>
                                    </div>
                                    <p class="text-teal fw-bold">$2000</p>
                                </div>
                                <div class="product-list bg-white align-items-center justify-content-between">
                                    <div class="d-flex align-items-center product-info" data-bs-toggle="modal"
                                        data-bs-target="#products">
                                        <a href="javascript:void(0);" class="img-bg">
                                            <img src="{{ asset('public/assets/img/products/pos-product-17.png') }}"
                                                alt="Products">
                                        </a>
                                        <div class="info">
                                            <h6><a href="javascript:void(0);">Iphone 11S</a></h6>
                                            <p>Quantity : 04</p>
                                        </div>
                                    </div>
                                    <p class="text-teal fw-bold">$3000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Products -->

    <div class="modal fade" id="create" tabindex="-1" aria-labelledby="create" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="pos-5.html">
                    <div class="modal-body pb-1">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-12">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end gap-2 flex-wrap">
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hold -->
    <div class="modal fade modal-default pos-modal" id="hold-order" aria-labelledby="hold-order">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hold order</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="pos-5.html">
                    <div class="modal-body">
                        <div class="bg-light br-10 p-4 text-center mb-3">
                            <h2 class="display-1">4500.00</h2>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Order Reference <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" value="" placeholder="">
                        </div>
                        <p>The current order will be set on hold. You can retreive this order from the pending order
                            button. Providing a reference to it might help you to identify the order more quickly.</p>
                    </div>
                    <div class="modal-footer d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Hold -->

    <!-- Edit Product -->
    <div class="modal fade modal-default pos-modal" id="edit-product" aria-labelledby="edit-product">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="pos-5.html">
                    <div class="modal-body pb-1">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="Red Nike Laser Show" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Product Price <span class="text-danger">*</span></label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="1800">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Tax Type <span class="text-danger">*</span></label>
                                    <select class="select">
                                        <option>Exclusive</option>
                                        <option>Inclusive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Tax <span class="text-danger">*</span></label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-percentage"></i>
                                        </span>
                                        <input type="text" class="form-control" value="15">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                                    <select class="select">
                                        <option>Percentage</option>
                                        <option>Early payment discounts</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Discount <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="15">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-12">
                                <div class="mb-3">
                                    <label class="form-label">Sale Unit <span class="text-danger">*</span></label>
                                    <select class="select">
                                        <option>Kilogram</option>
                                        <option>Grams</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end flex-wrap gap-2">
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Edit Product -->

    <!-- Delete Product -->
    <div class="modal fade modal-default" id="delete" aria-labelledby="payment-completed">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="success-wrap text-center">
                        <form action="pos-5.html">
                            <div class="icon-success bg-danger-transparent text-danger mb-2">
                                <i class="ti ti-trash"></i>
                            </div>
                            <h3 class="mb-2">Are you Sure!</h3>
                            <p class="fs-16 mb-3">The current order will be deleted as no payment has been made so
                                far.
                            </p>
                            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">No,
                                    Cancel</button>
                                <button type="submit" class="btn btn-md btn-primary">Yes, Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Product -->

    <!-- Reset -->
    <div class="modal fade modal-default" id="reset" aria-labelledby="payment-completed">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="success-wrap text-center">
                        <form action="pos-5.html">
                            <div class="icon-success bg-purple-transparent text-purple mb-2">
                                <i class="ti ti-transition-top"></i>
                            </div>
                            <h3 class="mb-2">Confirm Your Action</h3>
                            <p class="fs-16 mb-3">The current order will be cleared. But not deleted if it's
                                persistent. Would you like to proceed ?</p>
                            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">No,
                                    Cancel</button>
                                <button type="submit" class="btn btn-md btn-primary">Yes, Proceed</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Reset -->

    <!-- Recent Transactions -->
    <div class="modal fade pos-modal" id="recents" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Recent Transactions</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="tabs-sets">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="purchase-tab" data-bs-toggle="tab"
                                    data-bs-target="#purchase" type="button" aria-controls="purchase"
                                    aria-selected="true" role="tab">Purchase</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="payment-tab" data-bs-toggle="tab"
                                    data-bs-target="#payment" type="button" aria-controls="payment"
                                    aria-selected="false" role="tab">Payment</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="return-tab" data-bs-toggle="tab"
                                    data-bs-target="#return" type="button" aria-controls="return"
                                    aria-selected="false" role="tab">Return</button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="purchase" role="tabpanel"
                                aria-labelledby="purchase-tab">
                                <div class="card mb-0">
                                    <div
                                        class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                        <div class="search-set">
                                            <div class="search-input">
                                                <span class="btn-searchset"><i
                                                        class="ti ti-search fs-14 feather-search"></i></span>
                                            </div>
                                        </div>
                                        <ul class="table-top-head">
                                            <li>
                                                <a data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Pdf"><img
                                                        src="{{ asset('public/assets/img/icons/pdf.svg') }}"
                                                        alt="img"></a>
                                            </li>
                                            <li>
                                                <a data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Excel"><img
                                                        src="{{ asset('public/assets/img/icons/excel.svg') }}"
                                                        alt="img"></a>
                                            </li>
                                            <li>
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Print"><i
                                                        class="ti ti-printer"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table datatable border">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th class="no-sort">
                                                            <label class="checkboxs">
                                                                <input type="checkbox" class="select-all">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </th>
                                                        <th>Customer</th>
                                                        <th>Reference</th>
                                                        <th>Date</th>
                                                        <th>Amount </th>
                                                        <th class="no-sort">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-27.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Carl Evans</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0101</td>
                                                        <td>24 Dec 2024</td>
                                                        <td>$1000</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-02.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Minerva Rameriz</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0102</td>
                                                        <td>10 Dec 2024</td>
                                                        <td>$1500</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-05.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Robert Lamon</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0103</td>
                                                        <td>27 Nov 2024</td>
                                                        <td>$1500</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-22.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Patricia Lewis</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0104</td>
                                                        <td>18 Nov 2024</td>
                                                        <td>$2000</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-03.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Mark Joslyn</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0105</td>
                                                        <td>06 Nov 2024</td>
                                                        <td>$800</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-12.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Marsha Betts</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0106</td>
                                                        <td>25 Oct 2024</td>
                                                        <td>$750</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-06.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Daniel Jude</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0107</td>
                                                        <td>14 Oct 2024</td>
                                                        <td>$1300</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="payment" role="tabpanel">
                                <div class="card mb-0">
                                    <div
                                        class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                        <div class="search-set">
                                            <div class="search-input">
                                                <span class="btn-searchset"><i
                                                        class="ti ti-search fs-14 feather-search"></i></span>
                                            </div>
                                        </div>
                                        <ul class="table-top-head">
                                            <li>
                                                <a data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Pdf"><img
                                                        src="{{ asset('public/assets/img/icons/pdf.svg') }}"
                                                        alt="img"></a>
                                            </li>
                                            <li>
                                                <a data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Excel"><img
                                                        src="{{ asset('public/assets/img/icons/excel.svg') }}"
                                                        alt="img"></a>
                                            </li>
                                            <li>
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Print"><i
                                                        class="ti ti-printer"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table datatable border">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th class="no-sort">
                                                            <label class="checkboxs">
                                                                <input type="checkbox" class="select-all">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </th>
                                                        <th>Customer</th>
                                                        <th>Reference</th>
                                                        <th>Date</th>
                                                        <th>Amount </th>
                                                        <th class="no-sort">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-27.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Carl Evans</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0101</td>
                                                        <td>24 Dec 2024</td>
                                                        <td>$1000</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-02.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Minerva Rameriz</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0102</td>
                                                        <td>10 Dec 2024</td>
                                                        <td>$1500</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-05.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Robert Lamon</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0103</td>
                                                        <td>27 Nov 2024</td>
                                                        <td>$1500</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-22.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Patricia Lewis</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0104</td>
                                                        <td>18 Nov 2024</td>
                                                        <td>$2000</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-03.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Mark Joslyn</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0105</td>
                                                        <td>06 Nov 2024</td>
                                                        <td>$800</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-12.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Marsha Betts</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0106</td>
                                                        <td>25 Oct 2024</td>
                                                        <td>$750</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-06.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Daniel Jude</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0107</td>
                                                        <td>14 Oct 2024</td>
                                                        <td>$1300</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="return" role="tabpanel">
                                <div class="card mb-0">
                                    <div
                                        class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                        <div class="search-set">
                                            <div class="search-input">
                                                <span class="btn-searchset"><i
                                                        class="ti ti-search fs-14 feather-search"></i></span>
                                            </div>
                                        </div>
                                        <ul class="table-top-head">
                                            <li>
                                                <a data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Pdf"><img
                                                        src="{{ asset('public/assets/img/icons/pdf.svg') }}"
                                                        alt="img"></a>
                                            </li>
                                            <li>
                                                <a data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Excel"><img
                                                        src="{{ asset('public/assets/img/icons/excel.svg') }}"
                                                        alt="img"></a>
                                            </li>
                                            <li>
                                                <a data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Print"><i class="ti ti-printer"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table datatable border">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th class="no-sort">
                                                            <label class="checkboxs">
                                                                <input type="checkbox" class="select-all">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </th>
                                                        <th>Customer</th>
                                                        <th>Reference</th>
                                                        <th>Date</th>
                                                        <th>Amount </th>
                                                        <th class="no-sort">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-27.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Carl Evans</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0101</td>
                                                        <td>24 Dec 2024</td>
                                                        <td>$1000</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-02.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Minerva Rameriz</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0102</td>
                                                        <td>10 Dec 2024</td>
                                                        <td>$1500</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-05.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Robert Lamon</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0103</td>
                                                        <td>27 Nov 2024</td>
                                                        <td>$1500</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-22.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Patricia Lewis</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0104</td>
                                                        <td>18 Nov 2024</td>
                                                        <td>$2000</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-03.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Mark Joslyn</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0105</td>
                                                        <td>06 Nov 2024</td>
                                                        <td>$800</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-12.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Marsha Betts</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0106</td>
                                                        <td>25 Oct 2024</td>
                                                        <td>$750</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="checkboxs">
                                                                <input type="checkbox">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a href="javascript:void(0);"
                                                                    class="avatar avatar-md me-2">
                                                                    <img src="{{ asset('public/assets/img/users/user-06.jpg') }}"
                                                                        alt="product">
                                                                </a>
                                                                <a href="javascript:void(0);">Daniel Jude</a>
                                                            </div>
                                                        </td>
                                                        <td>INV/SL0107</td>
                                                        <td>14 Oct 2024</td>
                                                        <td>$1300</td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="me-2 edit-icon p-2"
                                                                    href="javascript:void(0);"><i data-feather="eye"
                                                                        class="feather-eye"></i></a>
                                                                <a class="me-2 p-2" href="javascript:void(0);"><i
                                                                        data-feather="edit"
                                                                        class="feather-edit"></i></a>
                                                                <a class="p-2" href="javascript:void(0);"><i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Recent Transactions -->

    <!-- Orders -->
    <div class="modal fade pos-modal" id="orders" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Orders</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="tabs-sets">
                        <ul class="nav nav-tabs" id="myTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="onhold-tab" data-bs-toggle="tab"
                                    data-bs-target="#onhold" type="button" aria-controls="onhold"
                                    aria-selected="true" role="tab">Onhold</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="unpaid-tab" data-bs-toggle="tab"
                                    data-bs-target="#unpaid" type="button" aria-controls="unpaid"
                                    aria-selected="false" role="tab">Unpaid</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="paid-tab" data-bs-toggle="tab"
                                    data-bs-target="#paid" type="button" aria-controls="paid"
                                    aria-selected="false" role="tab">Paid</button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="onhold" role="tabpanel"
                                aria-labelledby="onhold-tab">
                                <div class="input-icon-start pos-search position-relative mb-3">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Search Product">
                                </div>
                                <div class="order-body">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <span class="badge bg-dark fs-12 mb-2">Order ID : #45698</span>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <p class="fs-15 mb-1"><span
                                                            class="fs-14 fw-bold text-gray-9">Cashier :</span> admin
                                                    </p>
                                                    <p class="fs-15"><span class="fs-14 fw-bold text-gray-9">Total
                                                            :</span> $900</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="fs-15 mb-1"><span
                                                            class="fs-14 fw-bold text-gray-9">Customer :</span>
                                                        Botsford</p>
                                                    <p class="fs-15"><span class="fs-14 fw-bold text-gray-9">Date
                                                            :</span> 24 Dec 2024 13:39:11</p>
                                                </div>
                                            </div>
                                            <div class="bg-info-transparent p-1 rounded text-center my-3">
                                                <p class="text-info fw-medium">Customer need to recheck the product
                                                    once</p>
                                            </div>
                                            <div
                                                class="d-flex align-items-center justify-content-center flex-wrap gap-2">
                                                <a href="javascript:void(0);" class="btn btn-md btn-orange">Open
                                                    Order</a>
                                                <a href="javascript:void(0);" class="btn btn-md btn-teal"
                                                    data-bs-dismiss="modal" data-bs-toggle="modal"
                                                    data-bs-target="#products">View Products</a>
                                                <a href="javascript:void(0);"
                                                    class="btn btn-md btn-indigo">Print</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card bg-light mb-0">
                                        <div class="card-body">
                                            <span class="badge bg-dark fs-12 mb-2">Order ID : #666659</span>
                                            <div class="mb-3">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <p class="fs-15 mb-1"><span
                                                                class="fs-14 fw-bold text-gray-9">Cashier :</span>
                                                            admin</p>
                                                        <p class="fs-15"><span
                                                                class="fs-14 fw-bold text-gray-9">Total :</span> $900
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="fs-15 mb-1"><span
                                                                class="fs-14 fw-bold text-gray-9">Customer :</span>
                                                            Botsford</p>
                                                        <p class="fs-15"><span
                                                                class="fs-14 fw-bold text-gray-9">Date :</span> 24 Dec
                                                            2024 13:39:11</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="unpaid" role="tabpanel">
                                <div class="input-icon-start pos-search position-relative mb-3">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Search Product">
                                </div>
                                <div class="order-body">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <span class="badge bg-dark fs-12 mb-2">Order ID : #45698</span>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <p class="fs-15 mb-1"><span
                                                            class="fs-14 fw-bold text-gray-9">Cashier :</span> admin
                                                    </p>
                                                    <p class="fs-15"><span class="fs-14 fw-bold text-gray-9">Total
                                                            :</span> $900</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="fs-15 mb-1"><span
                                                            class="fs-14 fw-bold text-gray-9">Customer :</span>
                                                        Anastasia</p>
                                                    <p class="fs-15"><span class="fs-14 fw-bold text-gray-9">Date
                                                            :</span> 24 Dec 2024 13:39:11</p>
                                                </div>
                                            </div>
                                            <div class="bg-info-transparent p-1 rounded text-center my-3">
                                                <p class="text-info fw-medium">Customer need to recheck the product
                                                    once</p>
                                            </div>
                                            <div
                                                class="d-flex align-items-center justify-content-center flex-wrap gap-2">
                                                <a href="javascript:void(0);" class="btn btn-md btn-orange">Open
                                                    Order</a>
                                                <a href="javascript:void(0);" class="btn btn-md btn-teal"
                                                    data-bs-dismiss="modal" data-bs-toggle="modal"
                                                    data-bs-target="#products">View Products</a>
                                                <a href="javascript:void(0);"
                                                    class="btn btn-md btn-indigo">Print</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card bg-light mb-0">
                                        <div class="card-body">
                                            <span class="badge bg-dark fs-12 mb-2">Order ID : #666659</span>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <p class="fs-15 mb-1"><span
                                                            class="fs-14 fw-bold text-gray-9">Cashier :</span> admin
                                                    </p>
                                                    <p class="fs-15"><span class="fs-14 fw-bold text-gray-9">Total
                                                            :</span> $900</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="fs-15 mb-1"><span
                                                            class="fs-14 fw-bold text-gray-9">Customer :</span> Lucia
                                                    </p>
                                                    <p class="fs-15"><span class="fs-14 fw-bold text-gray-9">Date
                                                            :</span> 24 Dec 2024 13:39:11</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="paid" role="tabpanel">
                                <div class="input-icon-start pos-search position-relative mb-3">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Search Product">
                                </div>
                                <div class="order-body">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <span class="badge bg-dark fs-12 mb-2">Order ID : #45698</span>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <p class="fs-15 mb-1"><span
                                                            class="fs-14 fw-bold text-gray-9">Cashier :</span> admin
                                                    </p>
                                                    <p class="fs-15"><span class="fs-14 fw-bold text-gray-9">Total
                                                            :</span> $1000</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="fs-15 mb-1"><span
                                                            class="fs-14 fw-bold text-gray-9">Customer :</span> Hugo
                                                    </p>
                                                    <p class="fs-15"><span class="fs-14 fw-bold text-gray-9">Date
                                                            :</span> 24 Dec 2024 13:39:11</p>
                                                </div>
                                            </div>
                                            <div class="bg-info-transparent p-1 rounded text-center my-3">
                                                <p class="text-info fw-medium">Customer need to recheck the product
                                                    once</p>
                                            </div>
                                            <div
                                                class="d-flex align-items-center justify-content-center flex-wrap gap-2">
                                                <a href="javascript:void(0);" class="btn btn-md btn-orange">Open
                                                    Order</a>
                                                <a href="javascript:void(0);" class="btn btn-md btn-teal"
                                                    data-bs-dismiss="modal" data-bs-toggle="modal"
                                                    data-bs-target="#products">View Products</a>
                                                <a href="javascript:void(0);"
                                                    class="btn btn-md btn-indigo">Print</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card bg-light mb-0">
                                        <div class="card-body">
                                            <span class="badge bg-dark fs-12 mb-2">Order ID : #666659</span>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <p class="fs-15 mb-1"><span
                                                            class="fs-14 fw-bold text-gray-9">Cashier :</span> admin
                                                    </p>
                                                    <p class="fs-15"><span class="fs-14 fw-bold text-gray-9">Total
                                                            :</span> $9100</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="fs-15 mb-1"><span
                                                            class="fs-14 fw-bold text-gray-9">Customer :</span>
                                                        Antonio</p>
                                                    <p class="fs-15"><span class="fs-14 fw-bold text-gray-9">Date
                                                            :</span> 23 Dec 2024 13:39:11</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Orders -->

    <!-- Order Tax -->
    <div class="modal fade modal-default" id="order-tax">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Order Tax</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                {{-- Give the form an ID for easy targeting --}}
                <form id="order-tax-form">
                    <div class="modal-body pb-1">
                        <div class="mb-3">
                            <label class="form-label">Order Tax <span class="text-danger">*</span></label>
                            {{-- Give the select element an ID --}}
                            <select class="form-select" id="modal-tax-select">
                                <option value="" data-rate="0" data-name="None">No Tax</option>
                                @foreach ($taxes as $tax)
                                    <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}"
                                        data-name="{{ $tax->name }}">
                                        {{ $tax->name }} ({{ $tax->rate }}%)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end flex-wrap gap-2">
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary">Apply Tax</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Order Tax -->

    <!-- Shipping Cost -->
    <div class="modal fade modal-default" id="shipping-cost">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Shipping Cost</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                {{-- Give the form an ID --}}
                <form id="shipping-cost-form">
                    <div class="modal-body pb-1">
                        <div class="mb-3">
                            <label class="form-label">Shipping Cost ($) <span class="text-danger">*</span></label>
                            {{-- Give the input an ID --}}
                            <input type="number" step="0.01" id="modal-shipping-input" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end flex-wrap gap-2">
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary">Apply Shipping</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Shipping Cost -->

    <!-- Coupon Code -->
    <div class="modal fade modal-default" id="coupon-code">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Coupon Code</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="pos-5.html">
                    <div class="modal-body pb-1">
                        <div class="mb-3">
                            <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                            <select class="select">
                                <option>Select</option>
                                <option>NEWYEAR30</option>
                                <option>CHRISTMAS100</option>
                                <option>HALLOWEEN20</option>
                                <option>BLACKFRIDAY50</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end flex-wrap gap-2">
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Coupon Code -->

    <!-- Discount -->
    <div class="modal fade modal-default" id="discount">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Apply Discount</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                {{-- Give the form an ID --}}
                <form id="discount-form">
                    <div class="modal-body pb-1">
                        <div class="mb-3">
                            <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                            {{-- Give the select an ID --}}
                            <select class="form-select" id="modal-discount-type">
                                <option value="fixed">Flat ($)</option>
                                <option value="percentage">Percentage (%)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Value <span class="text-danger">*</span></label>
                            {{-- Give the input an ID --}}
                            <input type="number" step="0.01" id="modal-discount-value" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end flex-wrap gap-2">
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary">Apply Discount</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Discount -->

    <!-- Payment Cash -->
    <div class="modal fade modal-default" id="payment-cash">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Finalize Sale</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="pos-4.html">
                    <div class="modal-body pb-1">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Received Amount <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="1800">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Paying Amount <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="1800">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="change-item mb-3">
                                    <label class="form-label">Change</label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="0.00">
                                    </div>
                                </div>
                                <div class="point-item mb-3">
                                    <label class="form-label">Balance Point</label>
                                    <input type="text" class="form-control" value="200">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Type <span
                                            class="text-danger">*</span></label>
                                    <select class="select select-payment">
                                        <option value="credit">Credit Card</option>
                                        <option value="cash" selected>Cash</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="deposit">Deposit</option>
                                        <option value="points">Points</option>
                                    </select>
                                </div>
                                <div class="quick-cash payment-content bg-light  mb-3">
                                    <div class="d-flex align-items-center flex-wra gap-4">
                                        <h5 class="text-nowrap">Quick Cash</h5>
                                        <div class="d-flex align-items-center flex-wrap gap-3">
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash1" checked>
                                                <label class="btn btn-white" for="cash1">10</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash2">
                                                <label class="btn btn-white" for="cash2">20</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash3">
                                                <label class="btn btn-white" for="cash3">50</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash4">
                                                <label class="btn btn-white" for="cash4">100</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash5">
                                                <label class="btn btn-white" for="cash5">500</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash6">
                                                <label class="btn btn-white" for="cash6">1000</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="point-wrap payment-content mb-3">
                                    <div
                                        class=" bg-success-transparent d-flex align-items-center justify-content-between flex-wrap p-2 gap-2 br-5">
                                        <h6 class="fs-14 fw-bold text-success">You have 2000 Points to Use</h6>
                                        <a href="javascript:void(0);" class="btn btn-dark btn-md">Use for this
                                            Purchase</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Receiver</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Sale Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Staff Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end flex-wrap gap-2">
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Payment Cash  -->

    <!-- Payment Card  -->
    <div class="modal fade modal-default" id="payment-card">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Finalize Sale</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="pos-4.html">
                    <div class="modal-body pb-1">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Received Amount <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="1800">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Paying Amount <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="1800">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="change-item mb-3">
                                    <label class="form-label">Change</label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="0.00">
                                    </div>
                                </div>
                                <div class="point-item mb-3">
                                    <label class="form-label">Balance Point</label>
                                    <input type="text" class="form-control" value="200">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Type <span
                                            class="text-danger">*</span></label>
                                    <select class="select select-payment">
                                        <option value="credit" selected>Credit Card</option>
                                        <option value="cash">Cash</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="deposit">Deposit</option>
                                        <option value="points">Points</option>
                                    </select>
                                </div>
                                <div class="quick-cash payment-content bg-light  mb-3">
                                    <div class="d-flex align-items-center flex-wra gap-4">
                                        <h5 class="text-nowrap">Quick Cash</h5>
                                        <div class="d-flex align-items-center flex-wrap gap-3">
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash11" checked>
                                                <label class="btn btn-white" for="cash11">10</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash12">
                                                <label class="btn btn-white" for="cash12">20</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash13">
                                                <label class="btn btn-white" for="cash13">50</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash14">
                                                <label class="btn btn-white" for="cash14">100</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash15">
                                                <label class="btn btn-white" for="cash15">500</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash16">
                                                <label class="btn btn-white" for="cash16">1000</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="point-wrap payment-content mb-3">
                                    <div
                                        class=" bg-success-transparent d-flex align-items-center justify-content-between flex-wrap p-2 gap-2 br-5">
                                        <h6 class="fs-14 fw-bold text-success">You have 2000 Points to Use</h6>
                                        <a href="javascript:void(0);" class="btn btn-dark btn-md">Use for this
                                            Purchase</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Receiver</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Sale Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Staff Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end flex-wrap gap-2">
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Payment Card  -->

    <!-- Payment Cheque -->
    <div class="modal fade modal-default" id="payment-cheque">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Finalize Sale</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="pos-4.html">
                    <div class="modal-body pb-1">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Received Amount <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="1800">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Paying Amount <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="1800">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="change-item mb-3">
                                    <label class="form-label">Change</label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="0.00">
                                    </div>
                                </div>
                                <div class="point-item mb-3">
                                    <label class="form-label">Balance Point</label>
                                    <input type="text" class="form-control" value="200">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Type <span
                                            class="text-danger">*</span></label>
                                    <select class="select select-payment">
                                        <option value="credit">Credit Card</option>
                                        <option value="cash">Cash</option>
                                        <option value="cheque" selected>Cheque</option>
                                        <option value="deposit">Deposit</option>
                                        <option value="points">Points</option>
                                    </select>
                                </div>
                                <div class="quick-cash payment-content bg-light  mb-3">
                                    <div class="d-flex align-items-center flex-wra gap-4">
                                        <h5 class="text-nowrap">Quick Cash</h5>
                                        <div class="d-flex align-items-center flex-wrap gap-3">
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash21" checked>
                                                <label class="btn btn-white" for="cash21">10</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash22">
                                                <label class="btn btn-white" for="cash22">20</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash23">
                                                <label class="btn btn-white" for="cash23">50</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash24">
                                                <label class="btn btn-white" for="cash24">100</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash25">
                                                <label class="btn btn-white" for="cash25">500</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash26">
                                                <label class="btn btn-white" for="cash26">1000</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="point-wrap payment-content mb-3">
                                    <div
                                        class=" bg-success-transparent d-flex align-items-center justify-content-between flex-wrap p-2 gap-2 br-5">
                                        <h6 class="fs-14 fw-bold text-success">You have 2000 Points to Use</h6>
                                        <a href="javascript:void(0);" class="btn btn-dark btn-md">Use for this
                                            Purchase</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Receiver</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Sale Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Staff Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end flex-wrap gap-2">
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Payment Cheque -->

    <!--  Payment Deposit -->
    <div class="modal fade modal-default" id="payment-deposit">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Finalize Sale</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="pos-4.html">
                    <div class="modal-body pb-1">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Received Amount <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="1800">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Paying Amount <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="1800">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="change-item mb-3">
                                    <label class="form-label">Change</label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="0.00">
                                    </div>
                                </div>
                                <div class="point-item mb-3">
                                    <label class="form-label">Balance Point</label>
                                    <input type="text" class="form-control" value="200">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Type <span
                                            class="text-danger">*</span></label>
                                    <select class="select select-payment">
                                        <option value="credit">Credit Card</option>
                                        <option value="cash">Cash</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="deposit" selected>Deposit</option>
                                        <option value="points">Points</option>
                                    </select>
                                </div>
                                <div class="quick-cash payment-content bg-light  mb-3">
                                    <div class="d-flex align-items-center flex-wra gap-4">
                                        <h5 class="text-nowrap">Quick Cash</h5>
                                        <div class="d-flex align-items-center flex-wrap gap-3">
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash31" checked>
                                                <label class="btn btn-white" for="cash31">10</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash32">
                                                <label class="btn btn-white" for="cash32">20</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash33">
                                                <label class="btn btn-white" for="cash33">50</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash34">
                                                <label class="btn btn-white" for="cash34">100</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash35">
                                                <label class="btn btn-white" for="cash35">500</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash36">
                                                <label class="btn btn-white" for="cash36">1000</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="point-wrap payment-content mb-3">
                                    <div
                                        class=" bg-success-transparent d-flex align-items-center justify-content-between flex-wrap p-2 gap-2 br-5">
                                        <h6 class="fs-14 fw-bold text-success">You have 2000 Points to Use</h6>
                                        <a href="javascript:void(0);" class="btn btn-dark btn-md">Use for this
                                            Purchase</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Receiver</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Sale Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Staff Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end flex-wrap gap-2">
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Payment Deposit -->

    <!-- Payment Point -->
    <div class="modal fade modal-default" id="payment-points">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Finalize Sale</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="pos-4.html">
                    <div class="modal-body pb-1">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Received Amount <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="1800">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Paying Amount <span
                                            class="text-danger">*</span></label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="1800">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="change-item mb-3">
                                    <label class="form-label">Change</label>
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon text-gray-9">
                                            <i class="ti ti-currency-dollar"></i>
                                        </span>
                                        <input type="text" class="form-control" value="0.00">
                                    </div>
                                </div>
                                <div class="point-item mb-3">
                                    <label class="form-label">Balance Point</label>
                                    <input type="text" class="form-control" value="200">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Type <span
                                            class="text-danger">*</span></label>
                                    <select class="select select-payment">
                                        <option value="credit">Credit Card</option>
                                        <option value="cash">Cash</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="deposit">Deposit</option>
                                        <option value="points" selected>Points</option>
                                    </select>
                                </div>
                                <div class="quick-cash payment-content bg-light  mb-3">
                                    <div class="d-flex align-items-center flex-wra gap-4">
                                        <h5 class="text-nowrap">Quick Cash</h5>
                                        <div class="d-flex align-items-center flex-wrap gap-3">
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash41" checked>
                                                <label class="btn btn-white" for="cash41">10</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash42">
                                                <label class="btn btn-white" for="cash42">20</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash43">
                                                <label class="btn btn-white" for="cash43">50</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash44">
                                                <label class="btn btn-white" for="cash44">100</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash45">
                                                <label class="btn btn-white" for="cash45">500</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="btn-check" name="cash"
                                                    id="cash46">
                                                <label class="btn btn-white" for="cash46">1000</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="point-wrap payment-content mb-3">
                                    <div
                                        class=" bg-success-transparent d-flex align-items-center justify-content-between flex-wrap p-2 gap-2 br-5">
                                        <h6 class="fs-14 fw-bold text-success">You have 2000 Points to Use</h6>
                                        <a href="javascript:void(0);" class="btn btn-dark btn-md">Use for this
                                            Purchase</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Receiver</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Payment Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Sale Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Staff Note</label>
                                    <textarea class="form-control" rows="3" placeholder="Type your message"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end flex-wrap gap-2">
                        <button type="button" class="btn btn-md btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-md btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Payment Point -->

    <!-- Calculator -->
    <div class="modal fade pos-modal" id="calculator" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="calculator-wrap">
                        <div class="p-3">
                            <div class="d-flex align-items-center">
                                <h3>Calculator</h3>
                                <button type="button" class="close" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div>
                                <input class="input" type="text" placeholder="0" readonly>
                            </div>
                        </div>
                        <div class="calculator-body d-flex justify-content-between">
                            <div class="text-center">
                                <button class="btn btn-clear" onclick="clr()">C</button>
                                <button class="btn btn-number" onclick="dis('7')">7</button>
                                <button class="btn btn-number" onclick="dis('4')">4</button>
                                <button class="btn btn-number" onclick="dis('1')">1</button>
                                <button class="btn btn-number" onclick="dis(',')">,</button>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-expression" onclick="dis('/')">÷</button>
                                <button class="btn btn-number" onclick="dis('8')">8</button>
                                <button class="btn btn-number" onclick="dis('5')">5</button>
                                <button class="btn btn-number" onclick="dis('2')">2</button>
                                <button class="btn btn-number" onclick="dis('00')">00</button>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-expression" onclick="dis('%')">%</button>
                                <button class="btn btn-number" onclick="dis('9')">9</button>
                                <button class="btn btn-number" onclick="dis('6')">6</button>
                                <button class="btn btn-number" onclick="dis('3')">3</button>
                                <button class="btn btn-number" onclick="dis('.')">.</button>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-clear" onclick="back()"><i
                                        class="ti ti-backspace"></i></button>
                                <button class="btn btn-expression" onclick="dis('*')">x</button>
                                <button class="btn btn-expression" onclick="dis('-')">-</button>
                                <button class="btn btn-expression" onclick="dis('+')">+</button>
                                <button class="btn btn-clear" onclick="solve()">=</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Calculator -->

    <!-- Cash Register Details -->
    <div class="modal fade pos-modal" id="cash-register" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cash Register Details</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped border">
                            <tr>
                                <td>Cash in Hand</td>
                                <td class="text-gray-9 fw-medium text-end">$45689</td>
                            </tr>
                            <tr>
                                <td>Total Sale Amount</td>
                                <td class="text-gray-9 fw-medium text-end">$565597.88</td>
                            </tr>
                            <tr>
                                <td>Total Payment</td>
                                <td class="text-gray-9 fw-medium text-end">$566867.97</td>
                            </tr>
                            <tr>
                                <td>Cash Payment</td>
                                <td class="text-gray-9 fw-medium text-end">$3355.84</td>
                            </tr>
                            <tr>
                                <td>Total Sale Return</td>
                                <td class="text-gray-9 fw-medium text-end">$1959</td>
                            </tr>
                            <tr>
                                <td>Total Expense</td>
                                <td class="text-gray-9 fw-medium text-end">$0</td>
                            </tr>
                            <tr>
                                <td class="text-gray-9 fw-bold bg-secondary-transparent">Total Cash</td>
                                <td class="text-gray-9 fw-bold text-end bg-secondary-transparent">$587130.97</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2 flex-wrap">
                    <button type="button" class="btn btn-md btn-primary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Cash Register Details -->

    <!-- Today's Sale -->
    <div class="modal fade pos-modal" id="today-sale" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Today's Sale</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped border">
                            <tr>
                                <td>Total Sale Amount</td>
                                <td class="text-gray-9 fw-medium text-end">$565597.88</td>
                            </tr>
                            <tr>
                                <td>Cash Payment</td>
                                <td class="text-gray-9 fw-medium text-end">$3355.84</td>
                            </tr>
                            <tr>
                                <td>Credit Card Payment</td>
                                <td class="text-gray-9 fw-medium text-end">$1959</td>
                            </tr>
                            <tr>
                                <td>Cheque Payment:</td>
                                <td class="text-gray-9 fw-medium text-end">$0</td>
                            </tr>
                            <tr>
                                <td>Deposit Payment</td>
                                <td class="text-gray-9 fw-medium text-end">$565597.88</td>
                            </tr>
                            <tr>
                                <td>Points Payment</td>
                                <td class="text-gray-9 fw-medium text-end">$3355.84</td>
                            </tr>
                            <tr>
                                <td>Gift Card Payment</td>
                                <td class="text-gray-9 fw-medium text-end">$565597.88</td>
                            </tr>
                            <tr>
                                <td>Scan & Pay</td>
                                <td class="text-gray-9 fw-medium text-end">$3355.84</td>
                            </tr>
                            <tr>
                                <td>Pay Later</td>
                                <td class="text-gray-9 fw-medium text-end">$3355.84</td>
                            </tr>
                            <tr>
                                <td>Total Payment</td>
                                <td class="text-gray-9 fw-medium text-end">$565597.88</td>
                            </tr>
                            <tr>
                                <td>Total Sale Return</td>
                                <td class="text-gray-9 fw-medium text-end">$565597.88</td>
                            </tr>
                            <tr>
                                <td>Total Expense:</td>
                                <td class="text-gray-9 fw-medium text-end">$565597.88</td>
                            </tr>
                            <tr>
                                <td class="text-gray-9 fw-bold bg-secondary-transparent">Total Cash</td>
                                <td class="text-gray-9 fw-bold text-end bg-secondary-transparent">$587130.97</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2 flex-wrap">
                    <button type="button" class="btn btn-md btn-primary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Today's Sale -->

    <!-- Today's Profit -->
    <div class="modal fade pos-modal" id="today-profit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Today's Profit</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-center g-3 mb-3">
                        <div class="col-lg-4 col-md-6 d-flex">
                            <div class="border border-success bg-success-transparent br-8 p-3 flex-fill">
                                <p class="fs-16 text-gray-9 mb-1">Total Sale</p>
                                <h3 class="text-success">$89954</h3>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 d-flex">
                            <div class="border border-danger bg-danger-transparent br-8 p-3 flex-fill">
                                <p class="fs-16 text-gray-9 mb-1">Expense</p>
                                <h3 class="text-danger">$89954</h3>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 d-flex">
                            <div class="border border-info bg-info-transparent br-8 p-3 flex-fill">
                                <p class="fs-16 text-gray-9 mb-1">Total Profit </p>
                                <h3 class="text-info">$2145</h3>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped border">
                            <tr>
                                <td>Product Revenue</td>
                                <td class="text-gray-9 fw-medium text-end">$565597.88</td>
                            </tr>
                            <tr>
                                <td>Product Cost</td>
                                <td class="text-gray-9 fw-medium text-end">$3355.84</td>
                            </tr>
                            <tr>
                                <td>Expense</td>
                                <td class="text-gray-9 fw-medium text-end">$1959</td>
                            </tr>
                            <tr>
                                <td>Total Stock Adjustment</td>
                                <td class="text-gray-9 fw-medium text-end">$0</td>
                            </tr>
                            <tr>
                                <td>Deposit Payment</td>
                                <td class="text-gray-9 fw-medium text-end">$565597.88</td>
                            </tr>
                            <tr>
                                <td>Total Purchase Shipping Cost</td>
                                <td class="text-gray-9 fw-medium text-end">$3355.84</td>
                            </tr>
                            <tr>
                                <td>Total Sell Discount</td>
                                <td class="text-gray-9 fw-medium text-end">$565597.88</td>
                            </tr>
                            <tr>
                                <td>Total Sell Return</td>
                                <td class="text-gray-9 fw-medium text-end">$3355.84</td>
                            </tr>
                            <tr>
                                <td>Closing Stock</td>
                                <td class="text-gray-9 fw-medium text-end">$3355.84</td>
                            </tr>
                            <tr>
                                <td>Total Sales</td>
                                <td class="text-gray-9 fw-medium text-end">$565597.88</td>
                            </tr>
                            <tr>
                                <td>Total Sale Return</td>
                                <td class="text-gray-9 fw-medium text-end">$565597.88</td>
                            </tr>
                            <tr>
                                <td>Total Expense</td>
                                <td class="text-gray-9 fw-medium text-end">$565597.88</td>
                            </tr>
                            <tr>
                                <td class="text-gray-9 fw-bold bg-secondary-transparent">Total Cash</td>
                                <td class="text-gray-9 fw-bold text-end bg-secondary-transparent">$587130.97</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2 flex-wrap">
                    <button type="button" class="btn btn-md btn-primary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Today's Profit -->

    <!-- jQuery -->
    <script src="{{ asset('public/assets/js/jquery-3.7.1.min.js') }}"></script>

    <!-- Feather Icon JS -->
    <script src="{{ asset('public/assets/js/feather.min.js') }}"></script>

    <!-- Slimscroll JS -->
    <script src="{{ asset('public/assets/js/jquery.slimscroll.min.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('public/assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('public/assets/plugins/apexchart/chart-data.js') }}"></script>

    <!-- Datatable JS -->
    <script src="{{ asset('public/assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Daterangepikcer JS -->
    <script src="{{ asset('public/assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('public/assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <!-- Owl JS -->
    <script src="{{ asset('public/assets/plugins/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('public/assets/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Sticky-sidebar -->
    <script src="{{ asset('public/assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
    <script src="{{ asset('public/assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>

    <!-- Color Picker JS -->
    <script src="{{ asset('public/assets/plugins/@simonwep/pickr/pickr.es5.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('public/assets/js/theme-colorpicker.js') }}"></script>
    <script src="{{ asset('public/assets/js/calculator.js') }}"></script>
    <script src="{{ asset('public/assets/js/script.js') }}"></script>
    <script>
        $(document).ready(function() {
            // =========================================================================
            // CONFIG & STATE
            // =========================================================================
            const ORDER_STORAGE_KEY = 'pos_order';
            let order = {}; // This object holds the entire order state.

            // Cache jQuery selectors for performance
            const cartBody = $('#pos-cart-body');
            const emptyCartMessage = $('.empty-cart');
            const cartTableContainer = cartBody.closest('.table-responsive');

            // Initialize Bootstrap 5 modals
            const taxModal = new bootstrap.Modal(document.getElementById('order-tax'));
            const shippingModal = new bootstrap.Modal(document.getElementById('shipping-cost'));
            const discountModal = new bootstrap.Modal(document.getElementById('discount'));

            // Create a hidden input for discount type if it doesn't exist
            if (!$('#discount-type-hidden').length) {
                $('body').append('<input type="hidden" id="discount-type-hidden" value="fixed">');
            }

            // =========================================================================
            // HELPER FUNCTIONS FOR LOCALSTORAGE
            // =========================================================================
            function loadOrderFromStorage() {
                const storedOrder = localStorage.getItem(ORDER_STORAGE_KEY);
                order = storedOrder ? JSON.parse(storedOrder) : {
                    items: {},
                    shipping: 0,
                    discount: 0,
                    discount_type: 'fixed',
                    tax_rate: 0,
                    tax_name: 'None'
                };
            }

            function saveOrderToStorage() {
                localStorage.setItem(ORDER_STORAGE_KEY, JSON.stringify(order));
            }

            function clearOrder() {
                order = {
                    items: {},
                    shipping: 0,
                    discount: 0,
                    discount_type: 'fixed',
                    tax_rate: 0,
                    tax_name: 'None'
                };
                saveOrderToStorage();
                renderCart();
            }

            // =========================================================================
            // CORE UI & CALCULATION FUNCTIONS
            // =========================================================================
            function calculateTotals() {
                let subTotal = 0;
                for (const cartId in order.items) {
                    subTotal += order.items[cartId].price * order.items[cartId].quantity;
                }

                let discountAmount = (order.discount_type === 'percentage') ?
                    subTotal * (order.discount / 100) :
                    order.discount;

                const orderTaxAmount = subTotal * (order.tax_rate / 100);
                const grandTotal = subTotal + orderTaxAmount + order.shipping - discountAmount;

                $('#cart-subtotal').text(`$${subTotal.toFixed(2)}`);
                $('#cart-shipping').text(`$${order.shipping.toFixed(2)}`);
                $('#cart-tax').text(`$${orderTaxAmount.toFixed(2)}`);
                $('#tax-name-display').text(order.tax_name);
                $('#cart-discount').text(`-$${discountAmount.toFixed(2)}`);
                $('#cart-grandtotal').text(`$${grandTotal.toFixed(2)}`);

                const totalItems = Object.values(order.items).reduce((sum, item) => sum + item.quantity, 0);
                $('.badge:contains("Items") .text-teal').text(totalItems);
                $('.btn-block a').text(`Pay : $${grandTotal.toFixed(2)}`);
            }

            function renderCart() {
                cartBody.empty();
                $('.product-info').removeClass('active');

                if (Object.keys(order.items).length > 0) {
                    emptyCartMessage.hide();
                    cartTableContainer.show();
                } else {
                    emptyCartMessage.show();
                    cartTableContainer.hide();
                }

                for (const cartId in order.items) {
                    const item = order.items[cartId];
                    $(`.product-info[data-product-id="${item.id}"][data-variation-id="${item.variation_id || ''}"]`)
                        .addClass('active');

                    const itemSubTotal = item.price * item.quantity;
                    const rowHtml = `
                <tr data-product-id="${cartId}">
                    <td><h6 class="fs-16 fw-medium mb-1">${item.name}</h6></td>
                    <td><div class="qty-item m-0"><input type="number" class="form-control text-center cart-quantity" value="${item.quantity}" min="1"></div></td>
                    <td class="fw-bold">$${item.price.toFixed(2)}</td>
                    <td class="fw-bold">$${itemSubTotal.toFixed(2)}</td>
                    <td class="text-end"><a class="btn-icon delete-icon remove-item-btn" href="javascript:void(0);"><i class="ti ti-trash"></i></a></td>
                </tr>`;
                    cartBody.append(rowHtml);
                }
                calculateTotals();
            }

            // =========================================================================
            // EVENT LISTENERS
            // =========================================================================

            // --- CATEGORY FILTERING (Using Event Delegation for Owl Carousel compatibility) ---
            $('.pos-categories').on('click', '.tabs li', function() {
                const clickedTab = $(this);
                // We need to manually handle the active class for Owl Carousel
                $('.pos-categories .tabs li').removeClass('active');
                clickedTab.addClass('active');

                const categoryId = clickedTab.attr('id').replace('cat-', '');

                if (categoryId === 'all') {
                    $('.product-item').show();
                } else {
                    $('.product-item').hide();
                    $(`.product-item[data-category-id="${categoryId}"]`).show();
                }
            });

            // --- PRODUCT SEARCH ---
            $('.pos-search input').on('keyup', function() {
                let searchTerm = $(this).val().toLowerCase();
                $('.product-item').each(function() {
                    let productName = $(this).find('.product-content h6 a').text().toLowerCase();
                    if (productName.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // --- PRODUCT GRID CLICK (Select/Unselect) ---
            $('div.pos-products').on('click', 'div.product-info', function(event) {
                event.stopPropagation();
                const productData = $(this).data();
                const cartId = productData.productId + '-' + (productData.variationId || '0');
                if (order.items[cartId]) {
                    delete order.items[cartId];
                } else {
                    order.items[cartId] = {
                        id: productData.productId,
                        variation_id: productData.variationId,
                        name: productData.name,
                        price: parseFloat(productData.price),
                        quantity: 1
                    };
                }
                saveOrderToStorage();
                renderCart();
                return false;
            });

            // --- CART ITEM INTERACTIONS ---
            cartBody.on('input', '.cart-quantity', function() {
                // Get the unique ID of the item from the parent <tr>
                const cartId = $(this).closest('tr').data('product-id');
                const newQuantity = parseInt($(this).val());

                if (order.items[cartId]) {
                    if (newQuantity > 0) {
                        // Update quantity in the cart object
                        order.items[cartId].quantity = newQuantity;
                    } else {
                        // Remove the item if quantity is 0 or less
                        delete order.items[cartId];
                    }
                    // Persist the changes and redraw the entire UI
                    saveOrderToStorage();
                    renderCart();
                }
            });
            cartBody.on('click', '.remove-item-btn', function() {
                // Get the unique ID of the item from the parent <tr>
                const cartId = $(this).closest('tr').data('product-id');

                // Remove the item from the cart object
                if (order.items[cartId]) {
                    delete order.items[cartId];
                    // Persist the changes and redraw the entire UI
                    saveOrderToStorage();
                    renderCart();
                }
            });

            // --- MODAL LOGIC ---
            $('#order-tax-form').on('submit', function(e) {
                e.preventDefault();
                const selectedOption = $('#modal-tax-select').find('option:selected');
                order.tax_rate = parseFloat(selectedOption.data('rate')) || 0;
                order.tax_name = selectedOption.data('name') || 'None';
                saveOrderToStorage();
                calculateTotals();
                taxModal.hide();
            });
            $('#shipping-cost-form').on('submit', function(e) {
                e.preventDefault();
                order.shipping = parseFloat($('#modal-shipping-input').val()) || 0;
                saveOrderToStorage();
                calculateTotals();
                shippingModal.hide();
            });
            $('#discount-form').on('submit', function(e) {
                e.preventDefault();
                order.discount_type = $('#modal-discount-type').val();
                order.discount = parseFloat($('#modal-discount-value').val()) || 0;
                saveOrderToStorage();
                calculateTotals();
                discountModal.hide();
            });

            // --- SYNC MODALS ON OPEN ---
            $('#order-tax').on('show.bs.modal', () => {
                let tr = parseFloat(order.tax_rate);
                $('#modal-tax-select').find(
                    `option[data-rate="${tr.toFixed(2)}"]`).prop('selected', true)
            });
            $('#shipping-cost').on('show.bs.modal', () => $('#modal-shipping-input').val(order.shipping));
            $('#discount').on('show.bs.modal', () => {
                $('#modal-discount-type').val(order.discount_type);
                $('#modal-discount-value').val(order.discount);
            });

            // =========================================================================
            // INITIALIZATION
            // =========================================================================
            loadOrderFromStorage();
            renderCart();

            if ($('.owl-carousel').length > 0) {
                $('.owl-carousel').owlCarousel({
                    loop: false,
                    margin: 10,
                    nav: true,
                    dots: false,
                    responsive: {
                        0: {
                            items: 2
                        },
                        600: {
                            items: 4
                        },
                        1000: {
                            items: 6
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>
