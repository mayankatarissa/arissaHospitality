@extends('layouts.public')

@section('title', 'Staff management')

@section('content')
    <section class="container py-5">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div><p class="eyebrow mb-1">Operations</p><h1 class="display-6 fw-light">Staff</h1></div>
            <a class="btn btn-hotel" href="{{ route('management.staff.create') }}">Add staff member</a>
        </div>
        @if (session('status')) <div class="alert alert-success mt-4">{{ session('status') }}</div> @endif
        <div class="table-responsive mt-4">
            <table class="table align-middle">
                <thead><tr><th>Name</th><th>Position</th><th>Department</th><th>Manager</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($staff as $member)
                        <tr>
                            <td>{{ $member->name }}<div class="small text-secondary">{{ $member->email }} · {{ $member->phone }}</div></td>
                            <td>{{ $member->job_title }}</td>
                            <td>{{ $member->department ?: '—' }}</td>
                            <td>{{ $member->manager?->name ?: '—' }}</td>
                            <td>{{ ucfirst($member->status) }}</td>
                            <td class="text-nowrap">
                                <a class="btn btn-sm btn-outline-dark" href="{{ route('management.staff.edit', $member) }}">Edit</a>
                                @if (auth()->user()->role === 'admin')
                                    <form method="POST" action="{{ route('management.staff.destroy', $member) }}" class="d-inline" onsubmit="return confirm('Delete this staff record?')">
                                        @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-5">No staff records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $staff->links() }}
    </section>
@endsection
