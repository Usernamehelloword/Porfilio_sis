@extends('admin.layout')

@section('title', $designer->exists ? 'Edit — '.$designer->name : 'Add Designer')
@section('page_title', $designer->exists ? $designer->name : 'Add Designer')

@section('content')
<form method="POST" action="{{ $designer->exists ? route('admin.designers.update', $designer) : route('admin.designers.store') }}" enctype="multipart/form-data">
    @csrf
    @if ($designer->exists) @method('PUT') @endif

    <div class="card">
        <h3 class="card-title">Designer Profile</h3>
        <div class="form-grid">
            <div class="field">
                <label>FULL NAME *</label>
                <input type="text" name="name" value="{{ old('name', $designer->name) }}" required>
            </div>
            <div class="field">
                <label>POSITION</label>
                <input type="text" name="position" value="{{ old('position', $designer->position) }}" placeholder="Senior Architect">
            </div>
            <div class="field">
                <label>EMAIL</label>
                <input type="email" name="email" value="{{ old('email', $designer->email) }}">
            </div>
            <div class="field">
                <label>YEARS OF EXPERIENCE</label>
                <input type="text" name="years_experience" value="{{ old('years_experience', $designer->years_experience) }}" placeholder="10">
            </div>
            <div class="field">
                <label>SPECIALIZATION</label>
                <input type="text" name="specialization" value="{{ old('specialization', $designer->specialization) }}" placeholder="Residential architecture, sustainable design">
            </div>
            <div class="field">
                <label>LINKEDIN URL</label>
                <input type="url" name="linkedin" value="{{ old('linkedin', $designer->linkedin) }}">
            </div>
            <div class="field">
                <label>INSTAGRAM URL</label>
                <input type="url" name="instagram" value="{{ old('instagram', $designer->instagram) }}">
            </div>
            <div class="field">
                <label>PROFILE PHOTO</label>
                @if ($designer->photo)
                    <div class="current-media"><img src="{{ \App\Support\Content::imageUrl($designer->photo) }}" alt=""> <span>Current photo</span></div>
                @endif
                <input type="file" name="photo" accept="image/*">
            </div>
        </div>
        <div class="field">
            <label>BIOGRAPHY</label>
            <textarea name="bio" rows="5" placeholder="Alex focuses on residential architecture, sustainable design, and material-driven spaces.">{{ old('bio', $designer->bio) }}</textarea>
        </div>
        <label class="check">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $designer->is_published))> Published — appears on the public About / Team page
        </label>
    </div>

    <button class="btn-primary" type="submit">{{ $designer->exists ? 'Save Changes' : 'Add Designer' }}</button>
</form>
@endsection
