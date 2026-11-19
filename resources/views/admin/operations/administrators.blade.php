@extends('layouts.public')

@section('title', 'Administrator management')

@section('content')
    <section class="container py-5">
        <p class="eyebrow mb-1">Administration</p>
        <h1 class="display-6 fw-light">Administrators</h1>
        @if (session('status')) <div class="alert alert-success mt-4">{{ session('status') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger mt-4">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ route('admin.administrators.store') }}" class="card border-0 shadow-sm mt-4">
            @csrf
            <div class="card-body p-4">
                <h2 class="h5">Add administrator</h2>
                <p class="small text-secondary">Administrators have full system access. A password setup link will be sent to the new administrator.</p>
                <div class="row g-3">
                    <div class="col-md-4"><label for="name" class="form-label">Name</label><input id="name" name="name" value="{{ old('name') }}" class="form-control" required></div>
                    <div class="col-md-4"><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control" required></div>
                    <div class="col-md-4"><label for="phone" class="form-label">Phone</label><input id="phone" name="phone" value="{{ old('phone') }}" class="form-control"></div>
                </div>
                <button class="btn btn-hotel mt-3" type="submit">Create administrator</button>
            </div>
        </form>
        <div class="table-responsive mt-4">
            <table class="table align-middle">
                <thead><tr><th>Administrator</th><th>Status</th><th>Update details</th><th>Access</th></tr></thead>
                <tbody>
                    @foreach ($administrators as $administrator)
                        <tr>
                            <td><strong>{{ $administrator->name }}</strong><div class="small text-secondary">{{ $administrator->email }}</div></td>
                            <td>{{ $administrator->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.administrators.update', $administrator) }}" class="d-flex flex-wrap gap-2">
                                    @csrf @method('PUT')
                                    <input name="name" aria-label="Administrator name" value="{{ $administrator->name }}" class="form-control form-control-sm" required>
                                    <input name="email" aria-label="Administrator email" type="email" value="{{ $administrator->email }}" class="form-control form-control-sm" required>
                                    <input name="phone" aria-label="Administrator phone" value="{{ $administrator->phone }}" class="form-control form-control-sm">
                                    <button class="btn btn-sm btn-outline-dark" type="submit">Save</button>
                                </form>
                            </td>
                            <td>
                                @if (auth()->id() !== $administrator->id)
                                    <form method="POST" action="{{ route('admin.administrators.status', $administrator) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="is_active" value="{{ $administrator->is_active ? 0 : 1 }}">
                                        <button class="btn btn-sm {{ $administrator->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" type="submit">{{ $administrator->is_active ? 'Deactivate' : 'Activate' }}</button>
                                    </form>
                                @else
                                    <span class="small text-secondary">Current account</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $administrators->links() }}
    </section>
@endsection
