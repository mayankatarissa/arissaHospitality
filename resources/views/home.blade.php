@extends('layouts.public')

@section('title', 'A considered stay')

@section('content')
    <header class="hero py-5">
        <div class="container py-5">
            <div class="row py-lg-5">
                <div class="col-lg-7">
                    <p class="eyebrow text-white">Arissa Hospitality</p>
                    <h1 class="display-3 fw-light">A more thoughtful<br>way to stay.</h1>
                    <p class="lead col-md-10">Discover warm hospitality, considered details and a place to unwind at your own pace.</p>
                    <a href="{{ route('rooms.index') }}" class="btn btn-light px-4 mt-3">Explore our rooms</a>
                </div>
            </div>
        </div>
    </header>

    <section class="container py-5">
        <div class="card border-0 shadow p-3 p-lg-4 mt-n5 position-relative">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
                <div>
                    <p class="eyebrow mb-1">Plan your stay</p>
                    <h2 class="h4 mb-0">Find your room</h2>
                </div>
            </div>
            <x-room-search :filters="[]" />
        </div>
    </section>

    <section class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
            <div>
                <p class="eyebrow mb-1">Stay your way</p>
                <h2 class="display-6 fw-light">Rooms with room to breathe</h2>
            </div>
            <a href="{{ route('rooms.index') }}" class="link-dark">View all rooms <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            @forelse ($featuredRooms as $room)
                <div class="col-md-6 col-lg-4">
                    <article class="card room-card h-100">
                        @if ($room->image_path)
                            <img src="{{ $room->image_url }}" class="card-img-top" alt="{{ $room->name }}">
                        @else
                            <div class="room-placeholder"></div>
                        @endif
                        <div class="card-body p-4">
                            <p class="eyebrow mb-2">{{ $room->category->name }}</p>
                            <h3 class="h5">{{ $room->name }}</h3>
                            <p class="text-secondary">{{ \Illuminate\Support\Str::limit($room->description, 110) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>From <strong>{{ number_format((float) $room->price_per_night, 2) }}</strong> / night</span>
                                <a class="btn btn-outline-dark btn-sm" href="{{ route('rooms.show', $room) }}">Discover</a>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><p class="text-secondary">Rooms will be listed here as they are added to the hotel catalogue.</p></div>
            @endforelse
        </div>
    </section>

    <section class="container py-5">
        <div class="rounded-4 p-4 p-lg-5" style="background:#f5f2eb">
            <p class="eyebrow">The Arissa feeling</p>
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2 class="display-6 fw-light">Made for moments that matter.</h2>
                    <p class="mb-0 text-secondary">From a quiet morning to a memorable gathering, our team makes every part of your stay feel considered.</p>
                </div>
                <div class="col-lg-4 text-lg-end"><a href="{{ route('rooms.index') }}" class="btn btn-hotel px-4">Plan a stay</a></div>
            </div>
        </div>
    </section>
@endsection
