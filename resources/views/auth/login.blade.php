@extends('layouts.auth')

@push('css')
    <style>
        .card-wrapper {
            height: 507px;
            width: 874px;
            border-radius: 30px;
            padding: 0px 8px;
        }

        .logo {
            width: 120px;
        }

        .h-full {
            height: 100vh;
        }

        .h-fill {
            height: 100%;
        }

        .fill {
            height: 100%;
            width: 100%;
            max-width: 100%;
            max-height: 100%;
        }

        .login-text {
            color: #201E43;
            font-weight: 600;
            font-size: 16px;
        }

        .col-left {
            background-color: #201E43;
            border-radius: 21px;
        }

        .btn-main {
            padding: 10px 0px;
            width: 100%;
            text-align: center;
            color: #FFFFFF;
            background-color: #201E43;
            border-radius: 5px;
            border: none;
        }

        .title {
            font-weight: 600;
            font-size: 20px;
            color: #FFFFFF;
        }

        .z-1 {
            z-index: 10;
            margin-top: -140px;
        }

        .z-2 {
            z-index: 20;
            padding: 18px;
        }
    </style>
@endpush
@section('content')
    <div class="container-xl d-flex justify-content-center align-items-center h-full">
        <div class="card card-wrapper">
            <div class="card-body">
                <div class="row d-flex gap-3 h-fill">
                    {{-- col left --}}
                    <div class="col col-left d-flex flex-column align-items-start">
                        <div class="z-2">
                            <img src="{{ asset('assets/img/logo_real.png') }}" class="mb-3 logo" alt="">
                            <h2 class="mb-2 title">Welcome to REAL</h2>
                            <h2 class="mb-2 title">Asset Management!</h2>
                        </div>
                        <div class="z-1">
                            <img src="{{ asset('images/login.png') }}" class="fill" alt="">
                        </div>
                    </div>
                    {{-- col right --}}
                    <div class="col d-flex flex-column justify-content-center">
                        <img src="{{ asset('assets/img/logo_real.png') }}" class="mb-2 logo" alt="">
                        <h5 class="login-text mb-2">Please sign-in to your account.</h5>
                        <form class="mb-3" action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" autofocus />
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                    <a class="text-primary" href="{{ route('password.request') }}">
                                        <small>Forgot Password?</small>
                                    </a>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn-main d-grid w-100" type="submit">Sign in</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
