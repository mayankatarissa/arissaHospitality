@extends('layouts.public')

@section('title', $type === 'food' ? 'Food menu' : 'Bar menu')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Arissa dining</p>
        <h1 class="display-5 fw-light">{{ $type === 'food' ? 'Food menu' : 'Bar menu' }}</h1>
        <p class="text-secondary">A selection from our kitchen and bar. Menu items are informational and are not available for online ordering.</p>
        @if ($items->contains(fn ($item) => \Illuminate\Support\Str::startsWith($item->name, '[DEMO]')))
            <div class="alert alert-warning mt-3" role="status">Demo menu content and sample prices are shown. Update and verify all items before publishing your live menu.</div>
        @endif
        <div class="row g-3 mt-4">
            @forelse ($items as $item)
                <div class="col-md-6">
                    <article class="card border-0 shadow-sm h-100">
                        <div class="row g-0 h-100">
                            @if ($item->image_url)
                                <div class="col-4"><img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-100 h-100 object-fit-cover rounded-start"></div>
                            @endif
                            <div class="{{ $item->image_url ? 'col-8' : 'col-12' }}">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between gap-3"><h2 class="h5">{{ $item->name }}</h2><strong>{{ number_format((float) $item->price, 2) }}</strong></div>
                                    <p class="small text-uppercase text-secondary">{{ $item->category }}</p>
                                    <p class="text-secondary mb-0">{{ $item->description }}</p>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><p class="text-secondary">The menu is being refreshed. Please check back soon.</p></div>
            @endforelse
        </div>
    </section>
@endsection
