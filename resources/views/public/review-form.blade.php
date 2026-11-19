@extends('layouts.public')

@section('title', 'Write a review')

@section('content')
    <section class="container py-5" style="max-width:760px">
        <a href="{{ route('reviews.index') }}" class="small text-secondary text-decoration-none"><i class="bi bi-arrow-left"></i> Reviews</a>
        <h1 class="display-6 fw-light mt-3">Share your experience</h1>
        @if ($hasReview)
            <div class="alert alert-info mt-4">Each guest account may submit one review. Your existing review can be updated by contacting the hotel team.</div>
        @else
            @if ($errors->any()) <div class="alert alert-danger mt-4">{{ $errors->first() }}</div> @endif
            <form method="POST" action="{{ route('reviews.store') }}" class="card border-0 shadow-sm mt-4">
                @csrf
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <select id="rating" name="rating" class="form-select" required>
                            <option value="">Choose a rating</option>
                            @foreach (range(5, 1) as $rating)
                                <option value="{{ $rating }}" @selected(old('rating') == $rating)>{{ $rating }} star{{ $rating === 1 ? '' : 's' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label for="title" class="form-label">Title</label><input id="title" name="title" value="{{ old('title') }}" maxlength="255" class="form-control" required></div>
                    <div class="mb-3"><label for="description" class="form-label">Your review</label><textarea id="description" name="description" rows="5" maxlength="3000" class="form-control" required>{{ old('description') }}</textarea></div>
                    <button class="btn btn-hotel" type="submit">Submit for review</button>
                </div>
            </form>
        @endif
    </section>
@endsection
