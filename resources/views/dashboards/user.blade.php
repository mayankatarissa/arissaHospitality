@extends('layouts.public')

@section('title', 'My dashboard')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Your stay</p>
        <h1 class="display-6 fw-light">Welcome, {{ auth()->user()->name }}</h1>
        <p class="text-secondary">Explore our rooms and find a stay that suits you.</p>
        <div class="row g-3 my-4">
            @foreach ([
                ['Total rooms', $totalRooms, 'bi-door-open'],
                ['Approved bookings', $approvedBookings, 'bi-calendar-check'],
                ['Pending requests', $pendingBookings, 'bi-hourglass-split'],
                ['Available now', $availableRooms, 'bi-check2-circle'],
            ] as [$label, $value, $icon])
                <div class="col-6 col-lg-3">
                    <div class="card stat-card h-100 p-3">
                        <i class="bi {{ $icon }} fs-4"></i>
                        <span class="small text-secondary mt-2">{{ $label }}</span>
                        <strong class="h3 mb-0">{{ $value }}</strong>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="card border-0 shadow-sm p-3 p-lg-4">
            <p class="eyebrow mb-1">Plan your stay</p>
            <h2 class="h4 mb-4">Search rooms</h2>
            <x-room-search :filters="[]" />
        </div>
        <div class="mt-4">
            <a class="btn btn-outline-dark" href="{{ route('bookings.index') }}">View my bookings</a>
            <a class="btn btn-link" href="{{ route('rooms.index') }}">Browse all rooms</a>
        </div>
    </section>
@endsection
