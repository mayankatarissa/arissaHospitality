<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Arissa Hospitality') | {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        :root { --ink: #17283e; --gold: #b3955c; --sand: #f5f2eb; }
        body { color: var(--ink); background: #fff; }
        .navbar { background: rgba(255,255,255,.97); }
        .navbar-brand { color: var(--gold); letter-spacing: .14em; font-size: .9rem; }
        .nav-link { color: var(--ink); }
        .btn-hotel { color: #fff; background: var(--ink); border-color: var(--ink); }
        .btn-hotel:hover { color: #fff; background: #294361; border-color: #294361; }
        .eyebrow { color: var(--gold); letter-spacing: .16em; text-transform: uppercase; font-size: .76rem; font-weight: 700; }
        .hero { color: #fff; background: linear-gradient(90deg, rgba(14,29,44,.78), rgba(14,29,44,.18)), url('{{ asset('images/hotel-hero.jpg') }}') center/cover; }
        .account-menu { max-height: calc(100vh - 10rem); overflow-y: auto; overscroll-behavior: contain; }
        .room-placeholder { min-height: 210px; background: linear-gradient(135deg, #d9d3c7, #f3efe7); }
        .room-card { border: 0; border-radius: .9rem; overflow: hidden; box-shadow: 0 10px 30px rgba(23,40,62,.08); }
        .room-card img { height: 220px; object-fit: cover; }
        .stat-card { background: var(--sand); border: 0; border-radius: .8rem; }
        footer { background: var(--ink); color: #e8e4dc; }
        @media (min-width: 992px) {
            .navbar .dropdown:hover > .dropdown-menu,
            .navbar .dropdown:focus-within > .dropdown-menu { display: block; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
        @php
            $unreadNotificationCount = auth()->user()->unreadNotifications()->count();
            $notificationCount = auth()->user()->notifications()->count();
            $recentNotifications = auth()->user()->unreadNotifications()->latest()->limit(5)->get();
        @endphp
    @endauth
    <nav class="navbar navbar-expand-lg sticky-top border-bottom">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('home') }}">ARISSA HOSPITALITY</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavigation">
                <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <a class="nav-link" href="{{ route('rooms.index') }}">Rooms</a>
                    <a class="nav-link" href="{{ route('facilities.index') }}">Facilities</a>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Restaurant</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('restaurant.food') }}">Food menu</a></li>
                            <li><a class="dropdown-item" href="{{ route('restaurant.bar') }}">Bar menu</a></li>
                        </ul>
                    </div>
                    <a class="nav-link" href="{{ route('about') }}">About</a>
                    <a class="nav-link" href="{{ route('resort') }}">Resort</a>
                    <a class="nav-link" href="{{ route('events.index') }}">Events</a>
                    <a class="nav-link" href="{{ route('reviews.index') }}">Reviews</a>
                    <a class="nav-link" href="{{ route('contact') }}">Contact</a>
                    @auth
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        <div class="dropdown">
                            <button class="btn btn-link nav-link text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell"></i>
                                @if ($unreadNotificationCount > 0)
                                    <span class="badge rounded-pill text-bg-danger">{{ $unreadNotificationCount }}</span>
                                @elseif ($notificationCount > 0)
                                    <span class="badge rounded-pill text-bg-secondary">{{ $notificationCount }}</span>
                                @endif
                                <span class="visually-hidden">Notifications</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end p-2" style="min-width:300px">
                                @forelse ($recentNotifications as $notification)
                                    <li class="d-flex align-items-start gap-2 p-2 border-bottom">
                                        <form method="POST" action="{{ route('notifications.open', $notification) }}" class="flex-grow-1">
                                            @csrf
                                            <button class="btn btn-link small text-start text-decoration-none text-dark p-0 w-100" type="submit">
                                                {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                                <span class="d-block text-secondary">{{ $notification->created_at->diffForHumans() }}</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-link p-0" type="submit" aria-label="Mark notification as read"><i class="bi bi-check2"></i></button>
                                        </form>
                                    </li>
                                @empty
                                    <li class="small text-secondary p-2">You are all caught up.</li>
                                @endforelse
                                <li class="p-2"><a class="small" href="{{ route('notifications.index') }}">View all notifications</a></li>
                            </ul>
                        </div>
                        <div class="nav-item dropdown">
                            <button class="btn btn-link nav-link text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">My account</button>
                            <ul class="dropdown-menu dropdown-menu-end account-menu">
                                @if (in_array(auth()->user()->role, ['admin', 'manager'], true))
                                    <li><a class="dropdown-item" href="{{ route('management.bookings.index') }}">Manage bookings</a></li>
                                    <li><a class="dropdown-item" href="{{ route('management.staff.index') }}">Manage staff</a></li>
                                    @if (auth()->user()->role === 'admin')
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.rooms.index') }}">Manage rooms</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.catalog.index', 'categories') }}">Room categories</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.catalog.index', 'facilities') }}">Facilities</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.catalog.index', 'food') }}">Food menu</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.catalog.index', 'bar') }}">Bar menu</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.catalog.index', 'venues') }}">Event venues</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">Users</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.managers.index') }}">Managers</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.administrators.index') }}">Administrators</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.reviews.index') }}">Reviews</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.queries.index') }}">Queries</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.event-enquiries.index') }}">Event enquiries</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.content.edit') }}">Website content</a></li>
                                    @endif
                                @else
                                    <li><a class="dropdown-item" href="{{ route('bookings.index') }}">My bookings</a></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('notifications.index') }}">Notifications</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="px-3">
                                        @csrf
                                        <button class="btn btn-link p-0 text-decoration-none" type="submit">Sign out</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a class="nav-link" href="{{ route('login') }}">Sign in</a>
                        <a class="btn btn-hotel btn-sm px-3" href="{{ route('register') }}">Create account</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="py-4 mt-5">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-2 small">
            <span>Arissa Hospitality</span>
            <span>Thoughtful stays, lasting memories.</span>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
