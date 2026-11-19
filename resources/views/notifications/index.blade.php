@extends('layouts.public')

@section('title', 'Notifications')

@section('content')
    <section class="container py-5" style="max-width:900px">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div><p class="eyebrow mb-1">Updates</p><h1 class="display-6 fw-light mb-0">Notifications</h1></div>
            @if (auth()->user()->unreadNotifications()->exists())
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button class="btn btn-outline-dark btn-sm" type="submit">Mark all as read</button>
                </form>
            @endif
        </div>
        @if (session('status'))
            <div class="alert alert-success mt-4">{{ session('status') }}</div>
        @endif
        <ul class="list-group mt-4">
            @forelse ($notifications as $notification)
                <li class="list-group-item d-flex justify-content-between align-items-start gap-3 {{ $notification->read_at ? '' : 'list-group-item-light' }}">
                    <div>
                        <form method="POST" action="{{ route('notifications.open', $notification) }}">
                            @csrf
                            <button class="btn btn-link text-decoration-none text-dark text-start p-0" type="submit">
                                {{ $notification->data['message'] ?? 'You have a new notification.' }}
                            </button>
                        </form>
                        <span class="d-block small text-secondary">{{ $notification->created_at->format('M j, Y H:i') }}</span>
                    </div>
                    @if (! $notification->read_at)
                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary" type="submit">Mark read</button>
                        </form>
                    @endif
                </li>
            @empty
                <li class="list-group-item text-center text-secondary py-5">No notifications yet.</li>
            @endforelse
        </ul>
        <div class="mt-4">{{ $notifications->links() }}</div>
    </section>
@endsection
