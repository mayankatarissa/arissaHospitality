<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Account') | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; background: #f5f2eb; color: #17283e; }
        .auth-card { max-width: 470px; border: 0; border-radius: 1rem; }
        .brand { color: #a78342; letter-spacing: .12em; text-transform: uppercase; }
        .btn-hotel { background: #17283e; border-color: #17283e; color: #fff; }
        .btn-hotel:hover { background: #243d5b; border-color: #243d5b; color: #fff; }
    </style>
</head>
<body>
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <section class="card auth-card shadow-sm w-100">
            <div class="card-body p-4 p-md-5">
                <a class="brand text-decoration-none small fw-semibold" href="{{ url('/') }}">{{ config('app.name') }}</a>
                @if (session('status'))
                    <div class="alert alert-success mt-4" role="status">{{ session('status') }}</div>
                @endif
                @yield('content')
            </div>
        </section>
    </main>
</body>
</html>
