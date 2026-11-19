@extends('layouts.public')

@section('title', 'Contact queries')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Administration</p><h1 class="display-6 fw-light">Contact queries</h1>
        @if (session('status')) <div class="alert alert-success mt-4">{{ session('status') }}</div> @endif
        <div class="table-responsive mt-4">
            <table class="table align-middle">
                <thead><tr><th>Guest</th><th>Subject / message</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($queries as $query)
                        <tr>
                            <td>{{ $query->name }}<div class="small text-secondary">{{ $query->email }} · {{ $query->phone }}</div></td>
                            <td><strong>{{ $query->subject }}</strong><div class="small text-secondary">{{ \Illuminate\Support\Str::limit($query->message, 180) }}</div></td>
                            <td>{{ $query->created_at->format('M j, Y') }}</td>
                            <td>{{ ucfirst($query->status) }}</td>
                            <td class="text-nowrap">
                                <form method="POST" action="{{ route('admin.queries.status', $query) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $query->status === 'resolved' ? 'pending' : 'resolved' }}">
                                    <button class="btn btn-sm btn-outline-dark" type="submit">{{ $query->status === 'resolved' ? 'Reopen' : 'Resolve' }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.queries.destroy', $query) }}" class="d-inline" onsubmit="return confirm('Delete this query?')">
                                    @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-5">No contact queries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $queries->links() }}
    </section>
@endsection
