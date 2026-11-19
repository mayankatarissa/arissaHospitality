@extends('layouts.public')

@section('title', 'Guest reviews')

@section('content')
    <section class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-end">
            <div><p class="eyebrow mb-1">Guest stories</p><h1 class="display-5 fw-light">Reviews</h1></div>
            @auth
                @if (auth()->user()->role === 'user')
                    <a class="btn btn-outline-dark" href="{{ route('reviews.create') }}">Write a review</a>
                @endif
            @else
                <a class="btn btn-outline-dark" href="{{ route('login') }}">Sign in to review</a>
            @endauth
        </div>
        @if (session('status')) <div class="alert alert-success mt-4">{{ session('status') }}</div> @endif
        <div class="row g-4 mt-2">
            @forelse ($reviews as $review)
                <div class="col-md-6">
                    <article class="card border-0 shadow-sm h-100"><div class="card-body p-4">
                        <div class="text-warning" aria-label="{{ $review->rating }} out of 5 stars">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                        <h2 class="h5 mt-2">{{ $review->title }}</h2>
                        <p class="text-secondary">{{ $review->description }}</p>
                        <p class="small mb-0">{{ $review->user->name }} · {{ $review->created_at->format('M j, Y') }}</p>
                    </div></article>
                </div>
            @empty
                <div class="col-12"><p class="text-secondary">Approved guest reviews will appear here.</p></div>
            @endforelse
        </div>
        <div class="mt-4">{{ $reviews->links() }}</div>
    </section>
@endsection
