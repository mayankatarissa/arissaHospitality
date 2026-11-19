@extends('layouts.public')

@section('title', $content['title'] ?? $eyebrow)

@section('content')
    <section class="container py-5" style="max-width:950px">
        <p class="eyebrow">{{ $eyebrow }}</p>
        <h1 class="display-4 fw-light">{{ $content['title'] ?? 'Arissa Hospitality' }}</h1>
        <p class="lead text-secondary mt-4">{{ $content['body'] ?? 'Our team looks forward to welcoming you.' }}</p>
        @foreach (['mission' => 'Our mission', 'vision' => 'Our vision', 'experiences' => 'Experiences'] as $key => $label)
            @if (! empty($content[$key]))
                <div class="mt-5">
                    <h2 class="h4">{{ $label }}</h2>
                    <p class="text-secondary">{{ $content[$key] }}</p>
                </div>
            @endif
        @endforeach
        @if (! empty($content['contact_address']) || ! empty($content['contact_phone']) || ! empty($content['contact_email']))
            <section class="card border-0 shadow-sm mt-5">
                <div class="card-body p-4">
                    <p class="eyebrow mb-1">Contact</p>
                    <h2 class="h4">Get in touch</h2>
                    @if (! empty($content['contact_address']))
                        <address class="text-secondary mb-2">{{ $content['contact_address'] }}</address>
                    @endif
                    @if (! empty($content['contact_phone']))
                        <p class="mb-2"><strong>Phone:</strong> {{ $content['contact_phone'] }}</p>
                    @endif
                    @if (! empty($content['contact_email']))
                        <p class="mb-0"><strong>Email:</strong> <a href="mailto:{{ $content['contact_email'] }}">{{ $content['contact_email'] }}</a></p>
                    @endif
                </div>
            </section>
        @endif
        <a class="btn btn-hotel mt-4" href="{{ route('rooms.index') }}">Explore rooms</a>
    </section>
@endsection
