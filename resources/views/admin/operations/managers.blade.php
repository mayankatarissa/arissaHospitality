@extends('layouts.public')

@section('title', 'Manager management')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Administration</p>
        <h1 class="display-6 fw-light">Managers</h1>
        @if (session('status')) <div class="alert alert-success mt-4">{{ session('status') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger mt-4">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ route('admin.managers.store') }}" class="card border-0 shadow-sm mt-4">
            @csrf
            <div class="card-body p-4">
                <h2 class="h5">Add manager</h2>
                <p class="small text-secondary">A password setup link will be sent to the manager's email address.</p>
                <div class="row g-3">
                    <div class="col-md-4"><label for="name" class="form-label">Name</label><input id="name" name="name" value="{{ old('name') }}" class="form-control" required></div>
                    <div class="col-md-4"><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control" required></div>
                    <div class="col-md-4"><label for="phone" class="form-label">Phone</label><input id="phone" name="phone" value="{{ old('phone') }}" class="form-control"></div>
                </div>
                <button class="btn btn-hotel mt-3" type="submit">Create manager</button>
            </div>
        </form>
        <div class="table-responsive mt-4">
            <table class="table align-middle">
                <thead><tr><th>Manager</th><th>Phone</th><th>Account</th><th>Update details</th><th></th></tr></thead>
                <tbody>
                    @forelse ($managers as $manager)
                        <tr>
                            <td><strong>{{ $manager->name }}</strong><div class="small text-secondary">{{ $manager->email }}</div></td>
                            <td>{{ $manager->phone ?: '—' }}</td>
                            <td>{{ $manager->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.managers.update', $manager) }}" class="d-flex flex-wrap gap-2">
                                    @csrf @method('PUT')
                                    <input name="name" aria-label="Manager name" value="{{ $manager->name }}" class="form-control form-control-sm" required>
                                    <input name="email" aria-label="Manager email" type="email" value="{{ $manager->email }}" class="form-control form-control-sm" required>
                                    <input name="phone" aria-label="Manager phone" value="{{ $manager->phone }}" class="form-control form-control-sm">
                                    <button class="btn btn-sm btn-outline-dark" type="submit">Save</button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.managers.status', $manager) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $manager->is_active ? 0 : 1 }}">
                                    <button class="btn btn-sm {{ $manager->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" type="submit">{{ $manager->is_active ? 'Deactivate' : 'Activate' }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-5">No manager accounts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $managers->links() }}
    </section>
@endsection
