@extends('auth.layouts.app')
@section('title')
    Billing Software | Login
@endsection

@section('content')
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center bg-light">
        <div class="shadow bg-white" style="max-width: 1100px; width: 100%; border-radius: 10px;">

            <div class="row g-0">

                <!-- LEFT LOGIN SECTION -->
                <div class="col-md-6 p-5">

                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <img src="/assets/logo.png" alt="Logo" style="height: 60px;">
                    </div>

                    <h4 class="text-center fw-bold mb-4">LOGIN</h4>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" placeholder="Email address">

                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-2 d-flex justify-content-between">
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <a href="{{ route('password.request') }}" class="small text-primary align-self-center">
                            Forgot Password?
                        </a>
                        <br><br>
                        <!-- Login Button -->
                        <button class="mdc-button mdc-button--raised w-100 mdc-ripple-upgraded">
                            LOGIN
                        </button>

                        <!-- Signup -->
                        <div class="text-center mt-3">
                            <small>New Member? <a href="#" class="text-primary">Sign up Now</a></small>
                        </div>

                    </form>
                </div>

                <!-- RIGHT PANEL (Blue + Card) -->
                <div class="col-md-6 position-relative d-flex align-items-center justify-content-center"
                    style="background: linear-gradient(120deg, #1db7ff, #008cff); 
                        border-top-right-radius: 10px; border-bottom-right-radius: 10px;">

                    <!-- White Card -->
                    <div class="bg-white p-4 text-center shadow" style="width: 65%; border-radius: 10px;">

                        <div class="mb-3">
                            <img src="https://cdn-icons-png.flaticon.com/512/4144/4144407.png" alt="icon"
                                style="height: 60px;">
                        </div>

                        <h5 class="fw-bold">Title Here</h5>
                        <p class="text-muted small">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                            Iure, odio!
                        </p>

                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
