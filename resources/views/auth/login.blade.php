@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')
    <h1 class="h3 mt-4">Welcome back</h1>
    <p class="text-secondary">Sign in to manage your Arissa Hospitality account.</p>
    <form method="POST" action="{{ route('login.store') }}" class="mt-4">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="username">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-check mb-4">
            <input id="remember" name="remember" type="checkbox" class="form-check-input" value="1">
            <label for="remember" class="form-check-label">Remember me</label>
        </div>
        <button class="btn btn-hotel w-100" type="submit">Sign in</button>
    </form>
    <div class="d-flex justify-content-between mt-4 small">
        <a href="{{ route('password.request') }}">Forgot password?</a>
        <a href="{{ route('register') }}">Create an account</a>
    </div>
@endsection
