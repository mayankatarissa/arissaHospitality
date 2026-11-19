@extends('layouts.public')

@section('title', $record->exists ? 'Edit staff member' : 'Add staff member')

@section('content')
    <section class="container py-5" style="max-width:850px">
        <a href="{{ route('management.staff.index') }}" class="small text-secondary text-decoration-none"><i class="bi bi-arrow-left"></i> Staff</a>
        <h1 class="display-6 fw-light mt-3">{{ $record->exists ? 'Edit staff member' : 'Add staff member' }}</h1>
        @if ($errors->any()) <div class="alert alert-danger mt-4">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ $record->exists ? route('management.staff.update', $record) : route('management.staff.store') }}" class="card border-0 shadow-sm mt-4">
            @csrf
            @if ($record->exists) @method('PUT') @endif
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6"><label for="name" class="form-label">Name</label><input id="name" name="name" value="{{ old('name', $record->name) }}" class="form-control" required></div>
                    <div class="col-md-6"><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" value="{{ old('email', $record->email) }}" class="form-control"></div>
                    <div class="col-md-6"><label for="phone" class="form-label">Phone</label><input id="phone" name="phone" value="{{ old('phone', $record->phone) }}" class="form-control"></div>
                    <div class="col-md-6"><label for="job_title" class="form-label">Job title</label><input id="job_title" name="job_title" value="{{ old('job_title', $record->job_title) }}" class="form-control" required></div>
                    <div class="col-md-6"><label for="department" class="form-label">Department</label><input id="department" name="department" value="{{ old('department', $record->department) }}" class="form-control"></div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select" required>
                            @foreach (['active', 'inactive'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $record->status ?: 'active') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if (auth()->user()->role === 'admin')
                        <div class="col-md-6">
                            <label for="manager_id" class="form-label">Manager</label>
                            <select id="manager_id" name="manager_id" class="form-select">
                                <option value="">Unassigned</option>
                                @foreach ($managers as $manager)
                                    <option value="{{ $manager->id }}" @selected((string) old('manager_id', $record->manager_id) === (string) $manager->id)>{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-12"><label for="notes" class="form-label">Notes</label><textarea id="notes" name="notes" rows="4" class="form-control">{{ old('notes', $record->notes) }}</textarea></div>
                </div>
                <button class="btn btn-hotel mt-4" type="submit">{{ $record->exists ? 'Save changes' : 'Add staff member' }}</button>
            </div>
        </form>
    </section>
@endsection
