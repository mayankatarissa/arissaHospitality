@extends('layouts.public')

@section('title', $room->name)

@section('content')
    <section class="container py-5">
        <a class="small text-decoration-none text-secondary" href="{{ route('rooms.index') }}"><i class="bi bi-arrow-left"></i> All rooms</a>
        <div class="row g-5 mt-1">
            <div class="col-lg-7">
                @if ($room->image_path)
                    <img src="{{ $room->image_url }}" alt="{{ $room->name }}" class="w-100 rounded-4" style="max-height:520px;object-fit:cover">
                @else
                    <div class="room-placeholder rounded-4" style="min-height:420px"></div>
                @endif
            </div>
            <div class="col-lg-5">
                <p class="eyebrow mb-2">{{ $room->category->name }}</p>
                <h1 class="display-5 fw-light">{{ $room->name }}</h1>
                <p class="text-secondary">{{ $room->description ?: 'A welcoming space thoughtfully prepared for your stay.' }}</p>
                <p><i class="bi bi-people"></i> Maximum occupancy: {{ $room->max_occupancy }}</p>
                <hr>
                <h2 class="h6 text-uppercase">Room facilities</h2>
                @if ($room->facilities->isNotEmpty())
                    <ul class="list-unstyled d-flex flex-wrap gap-2">
                        @foreach ($room->facilities as $facility)
                            <li class="rounded-pill px-3 py-2" style="background:#f5f2eb">{{ $facility->name }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-secondary">Please contact us for room facility details.</p>
                @endif
                <div class="rounded-3 p-3 mt-4" style="background:#f5f2eb">
                    <span class="h4">{{ number_format((float) $room->price_per_night, 2) }}</span>
                    <span class="text-secondary"> per night</span>
                </div>
                @auth
                    @if (auth()->user()->role === 'user')
                        <form method="POST" action="{{ route('bookings.store', $room) }}" class="mt-4">
                            @csrf
                            <h2 class="h5">Request this room</h2>
                            <div class="mb-3">
                                <label for="check_in_at" class="form-label">Check-in</label>
                                <input id="check_in_at" name="check_in_at" type="datetime-local" min="{{ now()->format('Y-m-d\TH:i') }}" value="{{ old('check_in_at') }}" class="form-control @error('check_in_at') is-invalid @enderror" required>
                                @error('check_in_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="check_out_at" class="form-label">Check-out</label>
                                <input id="check_out_at" name="check_out_at" type="datetime-local" value="{{ old('check_out_at') }}" class="form-control @error('check_out_at') is-invalid @enderror" required>
                                @error('check_out_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="special_requests" class="form-label">Special requests <span class="text-secondary">(optional)</span></label>
                                <textarea id="special_requests" name="special_requests" rows="3" maxlength="2000" class="form-control @error('special_requests') is-invalid @enderror">{{ old('special_requests') }}</textarea>
                                @error('special_requests') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <button class="btn btn-hotel w-100" type="submit">Request booking</button>
                        </form>
                    @else
                        <p class="text-secondary mt-4">Sign in with a guest account to request this room.</p>
                    @endif
                @else
                    <a class="btn btn-hotel w-100 mt-3" href="{{ route('login') }}">Sign in to request this room</a>
                @endauth
            </div>
        </div>
    </section>
@endsection
