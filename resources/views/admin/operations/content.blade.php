@extends('layouts.public')

@section('title', 'Website content')

@section('content')
    <section class="container py-5" style="max-width:950px">
        <p class="eyebrow mb-1">Administration</p><h1 class="display-6 fw-light">Website content</h1>
        @if (session('status')) <div class="alert alert-success mt-4">{{ session('status') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger mt-4">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ route('admin.content.update') }}" class="card border-0 shadow-sm mt-4">
            @csrf @method('PUT')
            <div class="card-body p-4">
                @foreach (['about' => ['title' => 'About title', 'body' => 'Introduction', 'mission' => 'Mission', 'vision' => 'Vision', 'contact_address' => 'Sample address', 'contact_phone' => 'Sample phone', 'contact_email' => 'Sample email'], 'resort' => ['title' => 'Resort title', 'body' => 'Introduction', 'experiences' => 'Experiences']] as $group => $fields)
                    @php($section = $group === 'about' ? $about : $resort)
                    <h2 class="h4 mt-{{ $loop->first ? '0' : '5' }}">{{ ucfirst($group) }}</h2>
                    @foreach ($fields as $field => $label)
                        <div class="mb-3">
                            <label for="{{ $group }}_{{ $field }}" class="form-label">{{ $label }}</label>
                            @if ($field === 'title')
                                <input id="{{ $group }}_{{ $field }}" name="{{ $group }}[{{ $field }}]" value="{{ old($group.'.'.$field, $section[$field] ?? '') }}" class="form-control" required>
                            @elseif (in_array($field, ['contact_phone', 'contact_email'], true))
                                <input id="{{ $group }}_{{ $field }}" name="{{ $group }}[{{ $field }}]" type="{{ $field === 'contact_email' ? 'email' : 'tel' }}" value="{{ old($group.'.'.$field, $section[$field] ?? '') }}" class="form-control">
                            @else
                                <textarea id="{{ $group }}_{{ $field }}" name="{{ $group }}[{{ $field }}]" rows="3" class="form-control" @required(! in_array($field, ['contact_address'], true))>{{ old($group.'.'.$field, $section[$field] ?? '') }}</textarea>
                            @endif
                        </div>
                    @endforeach
                @endforeach
                <button class="btn btn-hotel" type="submit">Save website content</button>
            </div>
        </form>
    </section>
@endsection
