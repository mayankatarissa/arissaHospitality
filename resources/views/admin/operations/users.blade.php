@extends('layouts.public')

@section('title', 'User management')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Administration</p>
        <h1 class="display-6 fw-light">Users</h1>
        <div class="table-responsive mt-4">
            <table class="table align-middle">
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->phone ?: '—' }}</td><td>{{ $user->created_at->format('M j, Y') }}</td><td>{{ $user->is_active ? 'Active' : 'Inactive' }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-5">No guest accounts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </section>
@endsection
