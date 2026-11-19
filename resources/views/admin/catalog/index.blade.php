@extends('layouts.public')

@section('title', $title)

@section('content')
    <section class="container py-5">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div><p class="eyebrow mb-1">Administration</p><h1 class="display-6 fw-light">{{ $title }}</h1></div>
            <a class="btn btn-hotel" href="{{ route('admin.catalog.create', $type) }}">Add entry</a>
        </div>
        @if (session('status')) <div class="alert alert-success mt-4">{{ session('status') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger mt-4">{{ $errors->first() }}</div> @endif
        <div class="table-responsive mt-4">
            <table class="table align-middle">
                <thead><tr><th>Name</th><th>Details</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>
                                @if ($type === 'categories')
                                    {{ $item->default_occupancy ? 'Default occupancy: '.$item->default_occupancy : $item->description }}
                                @elseif (in_array($type, ['food', 'bar'], true))
                                    {{ $item->category }} · {{ number_format((float) $item->price, 2) }}
                                @elseif ($type === 'venues')
                                    {{ ucfirst($item->venue_type) }} · {{ $item->capacity ? $item->capacity.' guests' : 'Capacity not set' }}
                                @else
                                    {{ $item->operating_hours ?: $item->description }}
                                @endif
                            </td>
                            <td>
                                @if ($type === 'food' || $type === 'bar')
                                    {{ $item->is_available ? 'Available' : 'Unavailable' }}
                                @else
                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.catalog.edit', [$type, $item->id]) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.catalog.destroy', [$type, $item->id]) }}" class="d-inline" onsubmit="return confirm('Delete this entry?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-5">No entries have been added.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </section>
@endsection
