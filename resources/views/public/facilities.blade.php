@extends('layouts.public')

@section('title', 'Facilities')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Thoughtful details</p>
        <h1 class="display-5 fw-light">Facilities</h1>
        <p class="text-secondary">The comforts and services that make each stay feel easy.</p>
        <div class="row g-4 mt-2">
            @forelse ($facilities as $facility)
                <div class="col-sm-6 col-lg-4">
                    <article class="card room-card h-100">
                        @if ($facility->image_path)
                            <img src="{{ $facility->image_url }}" alt="{{ $facility->name }}" class="card-img-top">
                        @else
                            <div class="room-placeholder"></div>
                        @endif
                        <div class="card-body p-4">
                            <h2 class="h5">{{ $facility->name }}</h2>
                            <p class="text-secondary mb-0">{{ $facility->description }}</p>
                            @if ($facility->operating_hours)
                                <p class="small mt-3 mb-0"><i class="bi bi-clock"></i> {{ $facility->operating_hours }}</p>
                            @endif
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><p class="text-secondary">Facility information is being updated. Please contact our team for details.</p></div>
            @endforelse
        </div>
    </section>
@endsection
