@extends('layouts.public')

@section('title', 'Contact us')

@section('content')
    <section class="container py-5" style="max-width:850px">
        <p class="eyebrow mb-1">We are here to help</p>
        <h1 class="display-5 fw-light">Contact us</h1>
        <p class="text-secondary">Send a question to our guest services team.</p>
        @if (session('status')) <div class="alert alert-success mt-4">{{ session('status') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger mt-4">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ route('contact.store') }}" class="card border-0 shadow-sm mt-4">
            @csrf
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6"><label for="name" class="form-label">Name</label><input id="name" name="name" value="{{ old('name') }}" class="form-control" required maxlength="255"></div>
                    <div class="col-md-6"><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control" required maxlength="255"></div>
                    <div class="col-md-6"><label for="phone" class="form-label">Phone</label><input id="phone" name="phone" type="tel" value="{{ old('phone') }}" class="form-control" maxlength="30"></div>
                    <div class="col-md-6"><label for="subject" class="form-label">Subject</label><input id="subject" name="subject" value="{{ old('subject') }}" class="form-control" required maxlength="255"></div>
                    <div class="col-12"><label for="message" class="form-label">Message</label><textarea id="message" name="message" rows="5" maxlength="5000" class="form-control" required>{{ old('message') }}</textarea></div>
                </div>
                <button class="btn btn-hotel mt-4" type="submit">Send query</button>
            </div>
        </form>
    </section>
@endsection
