@extends('layouts.public')

@section('title', 'My bookings')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Your reservations</p>
        <h1 class="display-6 fw-light">My bookings</h1>
        @if (session('status'))
            <div class="alert alert-success mt-4">{{ session('status') }}</div>
        @endif
        <div class="table-responsive mt-4">
            <table class="table align-middle">
                <thead><tr><th>Booking</th><th>Room</th><th>Stay</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}<div class="small text-secondary">{{ $booking->created_at->format('M j, Y') }}</div></td>
                            <td>{{ $booking->room->name }}<div class="small text-secondary">{{ $booking->room->category->name }}</div></td>
                            <td>{{ $booking->check_in_at->format('M j, Y H:i') }}<br>{{ $booking->check_out_at->format('M j, Y H:i') }}</td>
                            <td><span class="badge text-bg-secondary">{{ ucfirst($booking->status) }}</span></td>
                            <td class="text-nowrap">
                                <a class="btn btn-sm btn-outline-dark" href="{{ route('bookings.show', $booking) }}">Details</a>
                                @can('cancel', $booking)
                                    <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="d-inline" onsubmit="return confirm('Cancel this booking request?')">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Cancel</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-5">You do not have any bookings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $bookings->links() }}
    </section>
@endsection
