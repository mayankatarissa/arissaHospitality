@extends('layouts.public')

@section('title', 'Review moderation')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Administration</p><h1 class="display-6 fw-light">Review moderation</h1>
        @if (session('status')) <div class="alert alert-success mt-4">{{ session('status') }}</div> @endif
        <div class="table-responsive mt-4">
            <table class="table align-middle">
                <thead><tr><th>Guest</th><th>Review</th><th>Rating</th><th>Submitted</th><th>Moderation</th></tr></thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td>{{ $review->user->name }}<div class="small text-secondary">{{ $review->user->email }}</div></td>
                            <td><strong>{{ $review->title }}</strong><div class="small text-secondary">{{ \Illuminate\Support\Str::limit($review->description, 160) }}</div></td>
                            <td>{{ $review->rating }} / 5</td>
                            <td>{{ $review->created_at->format('M j, Y') }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.reviews.status', $review) }}" class="d-flex gap-2">
                                    @csrf @method('PATCH')
                                    <select name="status" class="form-select form-select-sm">
                                        @foreach (['pending', 'approved', 'rejected'] as $status)
                                            <option value="{{ $status }}" @selected($review->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-dark" type="submit">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-5">No reviews submitted.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $reviews->links() }}
    </section>
@endsection
