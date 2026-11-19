@extends('layouts.public')

@section('title', 'Rooms')

@section('content')
    <section class="py-5" style="background:#f5f2eb">
        <div class="container">
            <p class="eyebrow mb-1">Stay a while</p>
            <h1 class="display-5 fw-light">Find your room</h1>
            <p class="text-secondary mb-0">Choose a room that feels right for your stay.</p>
        </div>
    </section>
    <section class="container py-4">
        <div class="card border-0 shadow-sm p-3 p-lg-4 mb-5">
            <x-room-search :filters="$filters" />
            @if ($errors->any())
                <div class="alert alert-danger mt-3 mb-0">{{ $errors->first() }}</div>
            @endif
        </div>
        @if ($hasDateSearch)
            <p class="text-secondary">Showing rooms without an overlapping approved booking for the selected period.</p>
        @endif
        <div class="row g-4">
            @forelse ($rooms as $room)
                <div class="col-md-6 col-lg-4">
                    <article class="card room-card h-100">
                        @if ($room->image_path)
                            <img src="{{ $room->image_url }}" class="card-img-top" alt="{{ $room->name }}">
                        @else
                            <div class="room-placeholder"></div>
                        @endif
                        <div class="card-body p-4 d-flex flex-column">
                            <p class="eyebrow mb-2">{{ $room->category->name }}</p>
                            <h2 class="h5">{{ $room->name }}</h2>
                            <p class="text-secondary">{{ \Illuminate\Support\Str::limit($room->description, 130) }}</p>
                            <p class="small mb-3">
                                <i class="bi bi-people"></i> Up to {{ $room->max_occupancy }}
                                @if ($room->facilities->isNotEmpty())
                                    <span class="mx-1">·</span>{{ $room->facilities->pluck('name')->join(', ') }}
                                @endif
                            </p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span><strong>{{ number_format((float) $room->price_per_night, 2) }}</strong> / night</span>
                                <a class="btn btn-outline-dark btn-sm" href="{{ route('rooms.show', $room) }}">View details</a>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <h2 class="h4">No matching rooms found</h2>
                    <p class="text-secondary">Try another date range or adjust your budget.</p>
                    <a href="{{ route('rooms.index') }}" class="btn btn-outline-dark">Clear search</a>
                </div>
            @endforelse
        </div>
        <div class="mt-4">{{ $rooms->links() }}</div>
    </section>
@endsection
