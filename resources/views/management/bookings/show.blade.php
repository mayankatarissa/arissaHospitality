@extends('layouts.public')

@section('title', 'Review booking')

@section('content')
    <section class="container py-5" style="max-width:950px">
        <a href="{{ route('management.bookings.index') }}" class="small text-secondary text-decoration-none"><i class="bi bi-arrow-left"></i> Booking requests</a>
        @if (session('status'))
            <div class="alert alert-success mt-4">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger mt-4">{{ $errors->first() }}</div>
        @endif
        <div class="d-flex justify-content-between align-items-start mt-3">
            <div><p class="eyebrow mb-1">Booking #{{ $booking->id }}</p><h1 class="display-6 fw-light">{{ $booking->room->name }}</h1></div>
            <span class="badge text-bg-secondary fs-6">{{ ucfirst($booking->status) }}</span>
        </div>
        <div class="card border-0 shadow-sm mt-4"><div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6"><span class="small text-secondary d-block">Customer</span>{{ $booking->user->name }}<br>{{ $booking->user->email }}<br>{{ $booking->user->phone }}</div>
                <div class="col-md-6"><span class="small text-secondary d-block">Room</span>{{ $booking->room->room_number }} · {{ $booking->room->category->name }}<br>{{ number_format((float) $booking->room->price_per_night, 2) }} per night</div>
                <div class="col-md-6"><span class="small text-secondary d-block">Check-in</span>{{ $booking->check_in_at->format('M j, Y H:i') }}</div>
                <div class="col-md-6"><span class="small text-secondary d-block">Check-out</span>{{ $booking->check_out_at->format('M j, Y H:i') }}</div>
                @if ($booking->special_requests)
                    <div class="col-12"><span class="small text-secondary d-block">Special requests</span>{{ $booking->special_requests }}</div>
                @endif
                @if ($booking->rejection_reason)
                    <div class="col-12"><span class="small text-secondary d-block">Rejection reason</span>{{ $booking->rejection_reason }}</div>
                @endif
            </div>
            @if ($booking->status === 'pending')
                <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                    <form method="POST" action="{{ route('management.bookings.approve', $booking) }}">
                        @csrf
                        <button class="btn btn-hotel" type="submit">Approve request</button>
                    </form>
                    <form method="POST" action="{{ route('management.bookings.reject', $booking) }}" class="flex-grow-1">
                        @csrf
                        <div class="input-group">
                            <input name="rejection_reason" class="form-control" maxlength="2000" placeholder="Reason (optional)">
                            <button class="btn btn-outline-danger" type="submit">Reject</button>
                        </div>
                    </form>
                </div>
            @endif
        </div></div>
        <h2 class="h5 mt-5">Status history</h2>
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
