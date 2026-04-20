<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Header -->
    @if (Route::has('login'))
        <nav class="navbar navbar-expand bg-white border-bottom">
            <div class="container">
                <span class="navbar-brand fw-semibold">{{ config('app.name', 'Laravel') }}</span>

                <div class="ms-auto d-flex gap-2">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline-dark btn-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-dark btn-sm">Register</a>
                        @endif
                    @endauth
                </div>
            </div>
        </nav>
    @endif

    <!-- Main Content -->
    <main class="container flex-grow-1 d-flex align-items-center py-5">
        <div class="row w-100 justify-content-center align-items-center g-4">

            <!-- Left Card -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="fw-semibold mb-3">Let's get started</h2>

                        <p class="text-secondary mb-4">
                            Laravel has an incredibly rich ecosystem.
                            We suggest starting with the following:
                        </p>

                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item px-0">
                                📘 Read the
                                <a href="https://laravel.com/docs" target="_blank" class="link-danger fw-medium">
                                    Documentation
                                </a>
                            </li>
                            <li class="list-group-item px-0">
                                🎥 Watch tutorials on
                                <a href="https://laracasts.com" target="_blank" class="link-danger fw-medium">
                                    Laracasts
                                </a>
                            </li>
                        </ul>

                        <a href="https://cloud.laravel.com" target="_blank" class="btn btn-dark">
                            Deploy Now
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Section -->
            <div class="col-lg-5 text-center">
                <div class="bg-white rounded shadow-sm p-4">
                    <svg width="200" viewBox="0 0 438 104" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.2036 -3H0V102.197H49.5189V86.7187H17.2036V-3Z" fill="#F53003" />
                        <path d="M438 -3H421.694V102.197H438V-3Z" fill="#F53003" />
                    </svg>

                    <p class="mt-3 text-muted">
                        Build modern apps with Laravel
                    </p>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center text-muted small py-3 border-top bg-white">
        © {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
