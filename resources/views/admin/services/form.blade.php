@extends('admin.layout')

@section('title', $service->exists ? 'Edit — '.$service->title : 'Add Service')
@section('page_title', $service->exists ? $service->title : 'Add Service')

@section('content')
<form method="POST" action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}" enctype="multipart/form-data">
    @csrf
    @if ($service->exists) @method('PUT') @endif

    <div class="card">
        <h3 class="card-title">Service Details</h3>
        <div class="form-grid">
            <div class="field">
                <label>TITLE *</label>
                <input type="text" name="title" value="{{ old('title', $service->title) }}" required>
            </div>
            <div class="field">
                <label>ICON (optional keyword, e.g. compass, pen, home)</label>
                <input type="text" name="icon" value="{{ old('icon', $service->icon) }}">
            </div>
        </div>
        <div class="field">
            <label>SHORT DESCRIPTION</label>
            <textarea name="short_description" rows="2">{{ old('short_description', $service->short_description) }}</textarea>
        </div>
        <div class="field">
            <label>FULL DESCRIPTION</label>
            <textarea name="full_description" class="rich" rows="6">{{ old('full_description', $service->full_description) }}</textarea>
        </div>
        <div class="field">
            <label>IMAGE</label>
            @if ($service->image)
                <div class="current-media"><img src="{{ \App\Support\Content::imageUrl($service->image) }}" alt=""> <span>Current image</span></div>
            @endif
            <input type="file" name="image" accept="image/*">
        </div>
        <label class="check">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $service->is_published))> Published — visible on the public Services page
        </label>
    </div>

    <button class="btn-primary" type="submit">{{ $service->exists ? 'Save Changes' : 'Create Service' }}</button>
</form>
@endsection
