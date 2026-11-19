@extends('layouts.public')

@section('title', 'Meetings and events')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Gather at Arissa</p>
        <h1 class="display-5 fw-light">Meetings &amp; events</h1>
        <p class="text-secondary">Flexible spaces and thoughtful planning for celebrations, meetings and special occasions.</p>
        <div class="row g-4 mt-2">
            @forelse ($venues as $venue)
                <div class="col-md-6 col-lg-4">
                    <article class="card room-card h-100">
                        @if ($venue->image_path)
                            <img src="{{ asset('storage/'.$venue->image_path) }}" alt="{{ $venue->name }}" class="card-img-top">
                        @else
                            <div class="room-placeholder"></div>
                        @endif
                        <div class="card-body p-4">
                            <p class="eyebrow">{{ ucfirst($venue->venue_type) }}</p>
                            <h2 class="h5">{{ $venue->name }}</h2>
                            <p class="text-secondary">{{ $venue->description }}</p>
                            @if ($venue->capacity)<p class="small mb-0">Capacity up to {{ $venue->capacity }} guests</p>@endif
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><p class="text-secondary">Event venue details are being updated.</p></div>
            @endforelse
        </div>
    </section>
    <section class="container pb-5" style="max-width:850px">
        <h2 class="h3 fw-light">Enquire about an event</h2>
        @if (session('status')) <div class="alert alert-success mt-3">{{ session('status') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger mt-3">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ route('events.enquiry') }}" class="card border-0 shadow-sm mt-3">
            @csrf
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6"><label for="name" class="form-label">Name</label><input id="name" name="name" value="{{ old('name') }}" class="form-control" required maxlength="255"></div>
                    <div class="col-md-6"><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control" required maxlength="255"></div>
                    <div class="col-md-6"><label for="phone" class="form-label">Phone</label><input id="phone" name="phone" value="{{ old('phone') }}" class="form-control" maxlength="30"></div>
                    <div class="col-md-6"><label for="event_type" class="form-label">Event type</label><input id="event_type" name="event_type" value="{{ old('event_type') }}" class="form-control" maxlength="100"></div>
                    <div class="col-md-6"><label for="event_date" class="form-label">Preferred date</label><input id="event_date" name="event_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('event_date') }}" class="form-control"></div>
                    <div class="col-md-6"><label for="guest_count" class="form-label">Estimated guests</label><input id="guest_count" name="guest_count" type="number" min="1" max="10000" value="{{ old('guest_count') }}" class="form-control"></div>
                    <div class="col-12"><label for="message" class="form-label">Tell us about your event</label><textarea id="message" name="message" rows="4" maxlength="5000" class="form-control" required>{{ old('message') }}</textarea></div>
                </div>
                <button class="btn btn-hotel mt-4" type="submit">Send event enquiry</button>
            </div>
        </form>
    </section>
@endsection
