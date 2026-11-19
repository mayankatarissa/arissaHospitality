@extends('layouts.public')

@section('title', 'Booking management')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Operations</p>
        <h1 class="display-6 fw-light">Booking requests</h1>
        <form method="GET" class="row g-2 align-items-end mt-3 mb-4">
            <div class="col-auto">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach (['pending', 'approved', 'rejected', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-dark" type="submit">Filter</button></div>
        </form>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Booking</th><th>Customer</th><th>Room</th><th>Stay</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}<div class="small text-secondary">{{ $booking->created_at->format('M j, Y') }}</div></td>
                            <td>{{ $booking->user->name }}<div class="small text-secondary">{{ $booking->user->email }}</div></td>
                            <td>{{ $booking->room->name }}<div class="small text-secondary">{{ $booking->room->room_number }}</div></td>
                            <td>{{ $booking->check_in_at->format('M j, Y H:i') }}<br>{{ $booking->check_out_at->format('M j, Y H:i') }}</td>
                            <td><span class="badge text-bg-secondary">{{ ucfirst($booking->status) }}</span></td>
                            <td><a class="btn btn-sm btn-outline-dark" href="{{ route('management.bookings.show', $booking) }}">Review</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-5">No booking requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $bookings->links() }}
    </section>
@endsection
