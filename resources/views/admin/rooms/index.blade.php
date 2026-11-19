@extends('layouts.public')

@section('title', 'Manage rooms')

@section('content')
    <section class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
            <div><p class="eyebrow mb-1">Administration</p><h1 class="display-6 fw-light mb-0">Room management</h1></div>
            <a class="btn btn-hotel" href="{{ route('admin.rooms.create') }}">Add a room</a>
        </div>
        @if (session('status'))
            <div class="alert alert-success mt-4">{{ session('status') }}</div>
        @endif
        <div class="table-responsive mt-4">
            <table class="table align-middle">
                <thead><tr><th>Room</th><th>Category</th><th>Price / night</th><th>Capacity</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($rooms as $room)
                        <tr>
                            <td>{{ $room->room_number }} · {{ $room->name }}</td>
                            <td>{{ $room->category->name }}</td>
                            <td>{{ number_format((float) $room->price_per_night, 2) }}</td>
                            <td>{{ $room->max_occupancy }}</td>
                            <td>{{ ucfirst($room->status) }}</td>
                            <td class="text-nowrap">
                                <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.rooms.edit', $room) }}">Edit</a>
                                @if ($room->status !== 'inactive')
                                    <form method="POST" action="{{ route('admin.rooms.deactivate', $room) }}" class="d-inline" onsubmit="return confirm('Deactivate this room?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Deactivate</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-5">No rooms have been added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $rooms->links() }}
    </section>
@endsection
