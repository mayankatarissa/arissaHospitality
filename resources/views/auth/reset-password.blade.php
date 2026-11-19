@extends('layouts.guest')

@section('title', 'Choose a new password')

@section('content')
    <h1 class="h3 mt-4">Choose a new password</h1>
    <form method="POST" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="email">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New password</label>
            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirm new password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required autocomplete="new-password">
        </div>
        <button class="btn btn-hotel w-100" type="submit">Reset password</button>
    </form>
@endsection
