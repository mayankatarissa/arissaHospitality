@extends('layouts.public')

@section('title', 'Event enquiries')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Administration</p><h1 class="display-6 fw-light">Event enquiries</h1>
        @if (session('status')) <div class="alert alert-success mt-4">{{ session('status') }}</div> @endif
        <div class="table-responsive mt-4">
            <table class="table align-middle">
                <thead><tr><th>Guest</th><th>Event</th><th>Message</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($enquiries as $enquiry)
                        <tr>
                            <td>{{ $enquiry->name }}<div class="small text-secondary">{{ $enquiry->email }} · {{ $enquiry->phone }}</div></td>
                            <td>{{ $enquiry->event_type ?: 'Event' }}<div class="small text-secondary">{{ $enquiry->event_date?->format('M j, Y') }} · {{ $enquiry->guest_count }} guests</div></td>
                            <td>{{ \Illuminate\Support\Str::limit($enquiry->message, 160) }}</td>
                            <td>{{ ucfirst($enquiry->status) }}</td>
                            <td class="text-nowrap">
                                <form method="POST" action="{{ route('admin.event-enquiries.status', $enquiry) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $enquiry->status === 'resolved' ? 'pending' : 'resolved' }}">
                                    <button class="btn btn-sm btn-outline-dark" type="submit">{{ $enquiry->status === 'resolved' ? 'Reopen' : 'Resolve' }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.event-enquiries.destroy', $enquiry) }}" class="d-inline" onsubmit="return confirm('Delete this event enquiry?')">
                                    @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-5">No event enquiries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $enquiries->links() }}
    </section>
@endsection
