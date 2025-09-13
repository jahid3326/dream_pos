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
        /* Add some padding to the auto-width items in our POS carousels */
        .pos-category4 .owl-item {
            padding: 0 10px !important;
            /* Adds 10px of space on the left and right of each tab */
        }

        .level .owl-carousel .owl-stage-outer {
            width: 100% !important;
        }

        .level .owl-carousel .owl-stage {
            width: 100% !important;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 5px;
        }

        .level .owl-carousel .owl-item {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 0px !important;
        }

        .product-image {
            width: 197px !important;
            height: 131px !important;
        }

        .product-image img {
            width: 100% !important;
        }

        .pos-carousel .owl-stage-outer {
            display: flex;
            justify-content: center;
        }

        .level {
            border-bottom: 1px solid #c7c3c3;
            margin-top: 5px;
        }

        .pos-products {
            margin-top: 10px;
        }

        /* Ensure all cards within a flex row have the same height */
        .product-grid-row {
            display: flex;
            flex-wrap: wrap;
        }

        .product-grid-row .product-item {
            display: flex;
            flex-direction: column;
            height: 100%;
            /* Make the column take full height */
        }

        .product-grid-row .product-item .card {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            /* Make the card fill the column height */
        }

        .product-grid-row .product-item .card .card-body {
            flex-grow: 1;
            /* Make the body fill the remaining space */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .owl-carousel .owl-nav {
            margin: 0;
            position: absolute;
            left: 0px;
            width: 100%;
            right: 0;
            top: 50% !important;
            transform: translate(0, -50%);
            display: flex;
            justify-content: space-between;
        }

        .owl-carousel .owl-nav button.owl-prev {
            margin-right: 2px;
        }

        .owl-carousel .owl-nav button.owl-next,
        .owl-carousel .owl-nav button.owl-prev {
            background-color: #092C4C !important;
            width: 22px !important;
            height: 22px !important;
            color: #fff;
        }

        .owl-carousel .owl-nav button.owl-next:hover,
        .owl-carousel .owl-nav button.owl-prev:hover {
            background-color: #125391 !important;
        }

        .form-control {
            border-color: #bcc2c7;
        }

        .form-select {
            border: 1px solid #bcc2c7;
        }

        .modal .close,
        .modal .btn-close {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            align-items: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            justify-content: center;
            -webkit-justify-content: center;
            -ms-flex-pack: center;
            color: #ffffff;
            opacity: 1;
            width: 20px;
            height: 20px;
            border: 0;
            font-weight: 700;
            background-color: transparent;
            border-radius: 50px;
        }

        .modal .close:hover,
        .modal .btn-close:hover {
            background-color: transparent;
            color: #ffffff;
        }
    </style>
</head>

<body class="pos-page">
    <!-- Main Wrapper -->
    <div class="main-wrapper pos-three pos-four">

        <!-- Header -->
        @include('pos._header')
        <!-- Header -->

        <div class="page-wrapper pos-pg-wrapper ms-0">
            <div class="content pos-design p-0">

                <div class="row align-items-start pos-wrapper">

                    @yield('content')

                </div>
            </div>
        </div>

    </div>
    <!-- /Main Wrapper -->

    <!-- Order Tax -->
    <div class="modal fade modal-default" id="order-tax">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Order Tax</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

    <!-- Discount -->
    <div class="modal fade modal-default" id="discount">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Apply Discount</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('public/assets/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('public/assets/plugins/sweetalert/sweetalerts.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('public/assets/js/theme-colorpicker.js') }}"></script>
    <script src="{{ asset('public/assets/js/calculator.js') }}"></script>
    <script src="{{ asset('public/assets/js/script.js') }}"></script>
    @stack('scripts')
</body>

</html>
