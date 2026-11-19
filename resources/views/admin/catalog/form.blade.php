@extends('layouts.public')

@section('title', ($record->exists ? 'Edit ' : 'Add ').$title)

@section('content')
    <section class="container py-5" style="max-width:850px">
        <a href="{{ route('admin.catalog.index', $type) }}" class="small text-secondary text-decoration-none"><i class="bi bi-arrow-left"></i> {{ $title }}</a>
        <h1 class="display-6 fw-light mt-3">{{ $record->exists ? 'Edit' : 'Add' }} {{ strtolower($title) }}</h1>
        @if ($errors->any()) <div class="alert alert-danger mt-4">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ $record->exists ? route('admin.catalog.update', [$type, $record->id]) : route('admin.catalog.store', $type) }}" enctype="multipart/form-data" class="card border-0 shadow-sm mt-4">
            @csrf
            @if ($record->exists) @method('PUT') @endif
            <div class="card-body p-4">
                <div class="row g-3">
                    @foreach ($fields as $name => $field)
                        <div class="{{ $field['type'] === 'textarea' ? 'col-12' : 'col-md-6' }}">
                            @if ($field['type'] === 'checkbox')
                                <input type="hidden" name="{{ $name }}" value="0">
                                <div class="form-check mt-4">
                                    <input id="{{ $name }}" name="{{ $name }}" type="checkbox" value="1" class="form-check-input" @checked(old($name, $record->getAttribute($name) ?? true))>
                                    <label for="{{ $name }}" class="form-check-label">{{ $field['label'] }}</label>
                                </div>
                            @else
                                <label for="{{ $name }}" class="form-label">{{ $field['label'] }}</label>
                                @if ($field['type'] === 'textarea')
                                    <textarea id="{{ $name }}" name="{{ $name }}" rows="4" class="form-control @error($name) is-invalid @enderror" @required($field['required'] ?? false)>{{ old($name, $record->getAttribute($name)) }}</textarea>
                                @else
                                    <input id="{{ $name }}" name="{{ $name }}" type="{{ $field['type'] }}" value="{{ $field['type'] === 'file' ? '' : old($name, $record->getAttribute($name)) }}" class="form-control @error($name) is-invalid @enderror" @required($field['required'] ?? false) @if ($field['type'] === 'file') accept=".jpg,.jpeg,.png,.webp" @endif>
                                @endif
                                @error($name) <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @endif
                        </div>
                    @endforeach
                </div>
                @if ($record->image_path)
                    <p class="small text-secondary mt-3">Current image: {{ basename($record->image_path) }}</p>
                @endif
                <button class="btn btn-hotel mt-4" type="submit">{{ $record->exists ? 'Save changes' : 'Create entry' }}</button>
            </div>
        </form>
    </section>
@endsection
