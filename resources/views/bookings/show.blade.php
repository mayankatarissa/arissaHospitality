@extends('layouts.public')

@section('title', 'Booking details')

@section('content')
    <section class="container py-5" style="max-width:900px">
        <a href="{{ route('bookings.index') }}" class="small text-secondary text-decoration-none"><i class="bi bi-arrow-left"></i> My bookings</a>
        @if (session('status'))
            <div class="alert alert-success mt-4">{{ session('status') }}</div>
        @endif
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-start mt-3">
            <div>
                <p class="eyebrow mb-1">Booking #{{ $booking->id }}</p>
                <h1 class="display-6 fw-light">{{ $booking->room->name }}</h1>
                <p class="text-secondary">{{ $booking->room->category->name }}</p>
            </div>
            <span class="badge text-bg-secondary fs-6">{{ ucfirst($booking->status) }}</span>
        </div>
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6"><span class="small text-secondary d-block">Check-in</span>{{ $booking->check_in_at->format('M j, Y \a\t H:i') }}</div>
                    <div class="col-md-6"><span class="small text-secondary d-block">Check-out</span>{{ $booking->check_out_at->format('M j, Y \a\t H:i') }}</div>
                    <div class="col-md-6"><span class="small text-secondary d-block">Requested on</span>{{ $booking->created_at->format('M j, Y H:i') }}</div>
                    <div class="col-md-6"><span class="small text-secondary d-block">Room price per night</span>{{ number_format((float) $booking->room->price_per_night, 2) }}</div>
                    @if ($booking->special_requests)
                        <div class="col-12"><span class="small text-secondary d-block">Special requests</span>{{ $booking->special_requests }}</div>
                    @endif
                    @if ($booking->rejection_reason)
                        <div class="col-12"><span class="small text-secondary d-block">Update from the hotel</span>{{ $booking->rejection_reason }}</div>
                    @endif
                </div>
                @can('cancel', $booking)
                    <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="mt-4" onsubmit="return confirm('Cancel this booking?')">
                        @csrf
                        <button class="btn btn-outline-danger" type="submit">Cancel booking</button>
                    </form>
                @endcan
            </div>
        </div>
        <h2 class="h5 mt-5">Booking updates</h2>
        <ul class="list-group mt-3">
            @foreach ($booking->statusHistories as $history)
                <li class="list-group-item d-flex justify-content-between gap-3">
                    <span>{{ ucfirst($history->new_status) }}@if ($history->note) — {{ $history->note }}@endif</span>
                    <span class="small text-secondary text-nowrap">{{ $history->created_at->format('M j, Y H:i') }}</span>
                </li>
            @endforeach
        </ul>
    </section>
@endsection
