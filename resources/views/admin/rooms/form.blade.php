@extends('layouts.public')

@section('title', $room->exists ? 'Edit room' : 'Add a room')

@section('content')
    <section class="container py-5" style="max-width:850px">
        <a href="{{ route('admin.rooms.index') }}" class="small text-secondary text-decoration-none"><i class="bi bi-arrow-left"></i> Room management</a>
        <h1 class="display-6 fw-light mt-3">{{ $room->exists ? 'Edit room' : 'Add a room' }}</h1>
        @if ($errors->any())
            <div class="alert alert-danger mt-4">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ $room->exists ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}" enctype="multipart/form-data" class="card border-0 shadow-sm mt-4">
            @csrf
            @if ($room->exists) @method('PUT') @endif
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="room_number" class="form-label">Room number</label>
                        <input id="room_number" name="room_number" value="{{ old('room_number', $room->room_number) }}" class="form-control @error('room_number') is-invalid @enderror" required maxlength="30">
                        @error('room_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="name" class="form-label">Room name</label>
                        <input id="name" name="name" value="{{ old('name', $room->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="room_category_id" class="form-label">Category</label>
                        <select id="room_category_id" name="room_category_id" class="form-select @error('room_category_id') is-invalid @enderror" required>
                            <option value="">Choose category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('room_category_id', $room->room_category_id) === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('room_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="price_per_night" class="form-label">Price per night</label>
                        <input id="price_per_night" name="price_per_night" type="number" min="0" step="0.01" value="{{ old('price_per_night', $room->price_per_night) }}" class="form-control @error('price_per_night') is-invalid @enderror" required>
                        @error('price_per_night') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="max_occupancy" class="form-label">Maximum occupancy</label>
                        <input id="max_occupancy" name="max_occupancy" type="number" min="1" max="100" value="{{ old('max_occupancy', $room->max_occupancy) }}" class="form-control @error('max_occupancy') is-invalid @enderror" required>
                        @error('max_occupancy') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach (['active', 'maintenance', 'inactive'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $room->status ?: 'active') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $room->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <fieldset class="col-12">
                        <legend class="form-label">Room facilities</legend>
                        <div class="d-flex flex-wrap gap-3">
                            @php
                                $selectedFacilities = old('facilities', $room->exists ? $room->facilities->modelKeys() : []);
                                $selectedFacilities = is_array($selectedFacilities) ? array_map('intval', $selectedFacilities) : [];
                            @endphp
                            @forelse ($facilities as $facility)
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="facilities[]" value="{{ $facility->id }}" @checked(in_array($facility->id, $selectedFacilities, true))>
                                    <span class="form-check-label">{{ $facility->name }}</span>
                                </label>
                            @empty
                                <p class="small text-secondary">Create facilities before assigning them to rooms.</p>
                            @endforelse
                        </div>
                        @error('facilities') <div class="text-danger small">{{ $message }}</div> @enderror
                    </fieldset>
                    <div class="col-12">
                        <label for="image" class="form-label">Room image <span class="text-secondary">(JPG, PNG or WebP; up to 5 MB)</span></label>
                        <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp" class="form-control @error('image') is-invalid @enderror">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <button class="btn btn-hotel mt-4" type="submit">{{ $room->exists ? 'Save changes' : 'Create room' }}</button>
            </div>
        </form>
    </section>
@endsection
