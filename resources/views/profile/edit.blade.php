@extends('layouts.public')

@section('title', 'Profile')

@section('content')
    <section class="container py-5" style="max-width:850px">
        <p class="eyebrow mb-1">My account</p>
        <h1 class="display-6 fw-light">Profile</h1>
        @if (session('status')) <div class="alert alert-success mt-4">{{ session('status') }}</div> @endif
        @if (session('password-status')) <div class="alert alert-success mt-4">{{ session('password-status') }}</div> @endif
        <form method="POST" action="{{ route('profile.update') }}" class="card border-0 shadow-sm mt-4">
            @csrf
            @method('PUT')
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Personal details</h2>
                <div class="mb-3"><label for="name" class="form-label">Name</label><input id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label for="phone" class="form-label">Phone</label><input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <button class="btn btn-hotel" type="submit">Save profile</button>
            </div>
        </form>
        <form method="POST" action="{{ route('profile.password') }}" class="card border-0 shadow-sm mt-4">
            @csrf
            @method('PUT')
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Change password</h2>
                @if ($errors->has('current_password') || $errors->has('password'))
                    <div class="alert alert-danger">{{ $errors->first('current_password') ?: $errors->first('password') }}</div>
                @endif
                <div class="mb-3"><label for="current_password" class="form-label">Current password</label><input id="current_password" name="current_password" type="password" class="form-control" required autocomplete="current-password"></div>
                <div class="mb-3"><label for="password" class="form-label">New password</label><input id="password" name="password" type="password" class="form-control" required autocomplete="new-password"></div>
                <div class="mb-3"><label for="password_confirmation" class="form-label">Confirm new password</label><input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required autocomplete="new-password"></div>
                <button class="btn btn-outline-dark" type="submit">Update password</button>
            </div>
        </form>
    </section>
@endsection
