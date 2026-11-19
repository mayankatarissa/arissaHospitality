@extends('layouts.public')

@section('title', 'Admin dashboard')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Administration</p>
        <h1 class="display-6 fw-light">Welcome, {{ auth()->user()->name }}</h1>
        <div class="row g-3 mt-2">
            @foreach ([
                ['Rooms', $totalRooms, 'bi-door-open'],
                ['Approved bookings', $approvedBookings, 'bi-calendar-check'],
                ['Pending requests', $pendingBookings, 'bi-hourglass-split'],
                ['Available now', $availableRooms, 'bi-check2-circle'],
                ['Guests', $totalUsers, 'bi-people'],
                ['Managers', $totalManagers, 'bi-person-badge'],
                ['Active staff', $totalStaff, 'bi-person-workspace'],
                ['Reviews', $totalReviews, 'bi-star'],
            ] as [$label, $value, $icon])
                <div class="col-6 col-md-4 col-xl-3"><div class="card stat-card h-100 p-3"><i class="bi {{ $icon }} fs-4"></i><span class="small text-secondary mt-2">{{ $label }}</span><strong class="h3 mb-0">{{ $value }}</strong></div></div>
            @endforeach
        </div>
        <div class="row g-4 mt-2">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100"><div class="card-body p-4">
                    <h2 class="h5">Booking requests · last 7 days</h2>
                    <canvas id="bookingActivity" aria-label="Bookings created in the last seven days" role="img"></canvas>
                </div></div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100"><div class="card-body p-4">
                    <h2 class="h5">Quick links</h2>
                    <div class="d-grid gap-2 mt-3">
                        <a class="btn btn-outline-dark text-start" href="{{ route('management.bookings.index') }}">Review booking requests</a>
                        <a class="btn btn-outline-dark text-start" href="{{ route('admin.rooms.index') }}">Manage rooms</a>
                        <a class="btn btn-outline-dark text-start" href="{{ route('admin.managers.index') }}">Manage managers</a>
                        <a class="btn btn-outline-dark text-start" href="{{ route('admin.administrators.index') }}">Manage administrators</a>
                        <a class="btn btn-outline-dark text-start" href="{{ route('management.staff.index') }}">Manage staff</a>
                        <a class="btn btn-outline-dark text-start" href="{{ route('admin.queries.index') }}">Guest queries</a>
                        <a class="btn btn-outline-dark text-start" href="{{ route('admin.reviews.index') }}">Review moderation</a>
                    </div>
                </div></div>
            </div>
        </div>
        <div class="card border-0 shadow-sm mt-4"><div class="card-body p-4">
            <h2 class="h5">Recent bookings</h2>
            <div class="table-responsive mt-3">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Guest</th><th>Room</th><th>Stay</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($recentBookings as $booking)
                            <tr><td>{{ $booking->user->name }}</td><td>{{ $booking->room->name }}</td><td>{{ $booking->check_in_at->format('M j') }} – {{ $booking->check_out_at->format('M j, Y') }}</td><td>{{ ucfirst($booking->status) }}</td><td><a href="{{ route('management.bookings.show', $booking) }}">Details</a></td></tr>
                        @empty
                            <tr><td colspan="5" class="text-secondary">No bookings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div></div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
    <script>
        new Chart(document.getElementById('bookingActivity'), {
            type: 'bar',
            data: {
                labels: @json($activityLabels),
                datasets: [{ label: 'Bookings', data: @json($activityValues), backgroundColor: '#b3955c', borderRadius: 6 }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, plugins: { legend: { display: false } } }
        });
    </script>
@endpush
