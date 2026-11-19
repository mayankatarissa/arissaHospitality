@extends('layouts.public')

@section('title', 'Manager dashboard')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Hotel operations</p>
        <h1 class="display-6 fw-light">Welcome, {{ auth()->user()->name }}</h1>
        <div class="row g-3 mt-2">
            @foreach ([
                ['Rooms', $totalRooms],
                ['Approved bookings', $approvedBookings],
                ['Pending requests', $pendingBookings],
                ['Available now', $availableRooms],
            ] as [$label, $value])
                <div class="col-6 col-lg-3"><div class="card stat-card p-3 h-100"><span class="small text-secondary">{{ $label }}</span><strong class="h3 mb-0">{{ $value }}</strong></div></div>
            @endforeach
        </div>
        <div class="row g-4 mt-2">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm"><div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center"><h2 class="h5 mb-0">Recent booking requests</h2><a href="{{ route('management.bookings.index') }}">View all</a></div>
                    <div class="table-responsive mt-3">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Guest</th><th>Room</th><th>Stay</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse ($recentBookings as $booking)
                                    <tr><td>{{ $booking->user->name }}</td><td>{{ $booking->room->name }}</td><td>{{ $booking->check_in_at->format('M j') }} – {{ $booking->check_out_at->format('M j, Y') }}</td><td>{{ ucfirst($booking->status) }}</td></tr>
                                @empty
                                    <tr><td colspan="4" class="text-secondary">No booking requests yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div></div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100"><div class="card-body p-4">
                    <h2 class="h5">Recent activity</h2>
                    <ul class="list-unstyled mt-3">
                        @forelse ($recentBookings->take(5) as $booking)
                            <li class="border-bottom py-2"><strong>{{ ucfirst($booking->status) }}</strong> · {{ $booking->user->name }}<span class="d-block small text-secondary">{{ $booking->created_at->diffForHumans() }}</span></li>
                        @empty
                            <li class="text-secondary">No recent activity.</li>
                        @endforelse
                    </ul>
                    <a class="btn btn-outline-dark" href="{{ route('management.staff.index') }}">Manage staff</a>
                </div></div>
            </div>
        </div>
    </section>
@endsection
