@extends('layouts.guest')

@section('title', 'Reset password')

@section('content')
    <h1 class="h3 mt-4">Reset your password</h1>
    <p class="text-secondary">Enter your account email and we will send a reset link.</p>
    <form method="POST" action="{{ route('password.email') }}" class="mt-4">
        @csrf
        <div class="mb-4">
            <label for="email" class="form-label">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="email">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <button class="btn btn-hotel w-100" type="submit">Email password reset link</button>
    </form>
    <p class="text-center small mt-4 mb-0"><a href="{{ route('login') }}">Return to sign in</a></p>
@endsection
