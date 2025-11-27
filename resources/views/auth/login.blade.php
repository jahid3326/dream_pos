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
    <title>{{ config('app.name') }} - Sign In</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/assets/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('public/assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('public/assets/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('public/assets/favicon/site.webmanifest') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/fontawesome/css/all.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">
    <style>
        .form-control {
            border-color: #9fa5a9;
        }

        .input-group-text {
            border-color: #9fa5a9;
        }

        .login-email input.is-invalid {
            border-right: none !important;
            background-image: unset !important;
        }

        .login-email span.is-icon-invalid {
            border: 1px solid rgb(194, 9, 9);
        }

        .login-password input.is-invalid {
            background-image: unset !important;
        }
    </style>
</head>

<body class="account-page bg-white">

    <div id="global-loader">
        <div class="whirly-loader"> </div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="account-content">
            <div class="row login-wrapper m-0">
                <div class="col-lg-6 p-0">
                    <div class="login-content">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="login-userset">
                                <div class="login-logo logo-normal">
                                    <img src="{{ asset('public/assets/img/logo.svg') }}" alt="img">
                                </div>
                                <a href="{{ route('dashboard') }}" class="login-logo logo-white">
                                    <img src="{{ asset('public/assets/img/logo-white.svg') }}" alt="Img">
                                </a>
                                <div class="login-userheading">
                                    <h3>Sign In</h3>
                                    <h4>Access the Dreamspos panel using your email and passcode.</h4>
                                </div>

                                {{-- Email Address Field --}}
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <div class="input-group login-email">
                                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                                            class="form-control border-end-0 @error('email') is-invalid @enderror">
                                        <span
                                            class="input-group-text border-start-0 @error('email') is-icon-invalid @enderror">
                                            <i class="ti ti-mail"></i>
                                        </span>
                                    </div>
                                    {{-- FIX: The error message block is moved OUTSIDE the input-group div --}}
                                    @error('email')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Password Field --}}
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="pass-group login-password">
                                        <input id="password" type="password" name="password"
                                            class="pass-input form-control @error('password') is-invalid @enderror"
                                            autocomplete="off">
                                        <span class="ti toggle-password ti-eye-off text-gray-9"></span>
                                    </div>
                                    {{-- FIX: The error message block is moved OUTSIDE the pass-group div --}}
                                    @error('password')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-login authentication-check">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="custom-control custom-checkbox">
                                                <label class="checkboxs ps-4 mb-0 pb-0 line-height-1">
                                                    <input type="checkbox">
                                                    <span class="checkmarks"></span>Remember me
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <a class="forgot-link" href="forgot-password-2.html">Forgot Password?</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-login">
                                    <button type="submit" class="btn btn-login">Sign In</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6 p-0">
                    <div class="login-img">
                        <img src="{{ asset('public/assets/img/authentication/authentication-01.svg') }}"
                            alt="img">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('public/assets/js/jquery-3.7.1.min.js') }}"></script>

    <!-- Feather Icon JS -->
    <script src="{{ asset('public/assets/js/feather.min.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('public/assets/js/script.js') }}"></script>

</body>

</html>
