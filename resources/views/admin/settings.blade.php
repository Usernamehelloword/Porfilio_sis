@extends('admin.layout')

@section('title', 'Settings')
@section('page_title', 'Website Settings')

@section('content')
<form method="POST" action="{{ route('admin.settings.general') }}" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <h3 class="card-title">General</h3>
        <div class="form-grid">
            <div class="field">
                <label>WEBSITE NAME</label>
                <input type="text" name="site_name" value="{{ old('site_name', \App\Support\Settings::get('site_name')) }}">
            </div>
            <div class="field">
                <label>STUDIO NAME</label>
                <input type="text" name="studio_name" value="{{ old('studio_name', \App\Support\Settings::get('studio_name')) }}">
            </div>
            <div class="field">
                <label>EMAIL</label>
                <input type="email" name="email" value="{{ old('email', \App\Support\Settings::get('email')) }}">
            </div>
            <div class="field">
                <label>PHONE</label>
                <input type="text" name="phone" value="{{ old('phone', \App\Support\Settings::get('phone')) }}">
            </div>
        </div>
        <div class="field">
            <label>ADDRESS</label>
            <textarea name="address" rows="2">{{ old('address', \App\Support\Settings::get('address')) }}</textarea>
        </div>
        <div class="form-grid">
            <div class="field"><label>INSTAGRAM</label><input type="url" name="instagram" value="{{ old('instagram', \App\Support\Settings::get('instagram')) }}"></div>
            <div class="field"><label>FACEBOOK</label><input type="url" name="facebook" value="{{ old('facebook', \App\Support\Settings::get('facebook')) }}"></div>
            <div class="field"><label>LINKEDIN</label><input type="url" name="linkedin" value="{{ old('linkedin', \App\Support\Settings::get('linkedin')) }}"></div>
            <div class="field"><label>YOUTUBE</label><input type="url" name="youtube" value="{{ old('youtube', \App\Support\Settings::get('youtube')) }}"></div>
        </div>
        <div class="form-grid">
            <div class="field">
                <label>LOGO</label>
                @if (\App\Support\Settings::get('logo'))
                    <div class="current-media"><img src="{{ \App\Support\Content::imageUrl(\App\Support\Settings::get('logo')) }}" alt=""> <span>Current logo</span></div>
                @endif
                <input type="file" name="logo" accept="image/*">
            </div>
            <div class="field">
                <label>FAVICON</label>
                @if (\App\Support\Settings::get('favicon'))
                    <div class="current-media"><img src="{{ \App\Support\Content::imageUrl(\App\Support\Settings::get('favicon')) }}" alt=""> <span>Current favicon</span></div>
                @endif
                <input type="file" name="favicon" accept="image/*">
            </div>
        </div>
        <button class="btn-primary" type="submit">Save General Settings</button>
    </div>
</form>

<form method="POST" action="{{ route('admin.settings.seo') }}" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <h3 class="card-title">SEO</h3>
        <div class="field">
            <label>WEBSITE TITLE</label>
            <input type="text" name="seo_title" value="{{ old('seo_title', \App\Support\Settings::get('seo_title')) }}">
        </div>
        <div class="field">
            <label>META DESCRIPTION</label>
            <textarea name="meta_description" rows="3">{{ old('meta_description', \App\Support\Settings::get('meta_description')) }}</textarea>
        </div>
        <div class="field">
            <label>KEYWORDS (comma separated)</label>
            <input type="text" name="meta_keywords" value="{{ old('meta_keywords', \App\Support\Settings::get('meta_keywords')) }}">
        </div>
        <div class="field">
            <label>OPEN GRAPH IMAGE</label>
            @if (\App\Support\Settings::get('og_image'))
                <div class="current-media"><img src="{{ \App\Support\Content::imageUrl(\App\Support\Settings::get('og_image')) }}" alt=""> <span>Current image</span></div>
            @endif
            <input type="file" name="og_image" accept="image/*">
        </div>
        <button class="btn-primary" type="submit">Save SEO Settings</button>
    </div>
</form>
@endsection
