@extends('layouts.auth')

@section('title', 'Reset Password')

@push('styles')
    <style>
        /* Wider cap for very large screens */
        @media (min-width: 1200px) {
            .auth-card {
                max-width: 560px;
            }
        }

        @media (min-width: 765px) {
            .auth-card {
                max-width: 480px;
                margin: 0 auto;
                border-radius: 10px;
                box-shadow: 0 6px 18px rgba(22, 38, 62, 0.08);
                overflow: hidden;
            }

            .auth-card .card-header {
                background: #fff;
                border-bottom: 1px solid #eef2f6;
                padding: 18px 20px;
                font-weight: 500;
            }

            .auth-card .card-body {
                padding: 18px 20px;
            }

            .auth-card .btn-back {
                background: #07243f;
                border-color: #07243f;
                color: #fff;
                box-shadow: none;
            }

            .auth-card .btn-reset {
                background: #ff9f43;
                border-color: #ff9f43;
                color: #fff;
            }

            .auth-card .d-flex.actions {
                gap: 12px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card auth-card">
                    <div class="card-header">Reset Password</div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input id="email" type="email" name="email" class="form-control" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input id="password" type="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                    class="form-control" required>
                            </div>

                            <div class="d-flex justify-content-between align-items-center actions">
                                <a href="{{ route('login') }}" class="btn btn-secondary btn-back">Back to login</a>
                                <button type="submit" class="btn btn-primary btn-reset">Reset Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
