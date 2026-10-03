@extends('admin.layout')

@section('title', 'About Page')
@section('page_title', 'About Page')

@section('content')
<form method="POST" action="{{ route('admin.about.update') }}" enctype="multipart/form-data">
    @csrf

    <div class="card">
        <h3 class="card-title">Studio Introduction</h3>
        <div class="field">
            <label>TITLE</label>
            <input type="text" name="about_title" value="{{ old('about_title', \App\Support\Settings::get('about_title')) }}">
        </div>
        <div class="field">
            <label>DESCRIPTION</label>
            <textarea name="about_description" rows="3">{{ old('about_description', \App\Support\Settings::get('about_description')) }}</textarea>
        </div>
        <div class="field">
            <label>SERIF QUOTE</label>
            <textarea name="about_quote" rows="2">{{ old('about_quote', \App\Support\Settings::get('about_quote')) }}</textarea>
        </div>
        <div class="field">
            <label>BODY PARAGRAPH 1</label>
            <textarea name="about_body_1" rows="4">{{ old('about_body_1', \App\Support\Settings::get('about_body_1')) }}</textarea>
        </div>
        <div class="field">
            <label>BODY PARAGRAPH 2</label>
            <textarea name="about_body_2" rows="4">{{ old('about_body_2', \App\Support\Settings::get('about_body_2')) }}</textarea>
        </div>
        <div class="form-grid">
            <div class="field">
                <label>PORTRAIT IMAGE</label>
                @if (\App\Support\Settings::get('portrait_image'))
                    <div class="current-media"><img src="{{ \App\Support\Content::imageUrl(\App\Support\Settings::get('portrait_image')) }}" alt=""> <span>Current image</span></div>
                @endif
                <input type="file" name="portrait_image" accept="image/*">
            </div>
            <div class="field">
                <label>PORTRAIT CAPTION</label>
                <input type="text" name="portrait_caption" value="{{ old('portrait_caption', \App\Support\Settings::get('portrait_caption')) }}">
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">Statistics</h3>
        <div class="form-grid">
            @foreach (array_pad(\App\Support\Settings::json('stats') ?: \App\Support\Studio::stats(), 4, ['value' => '', 'label' => '']) as $i => $stat)
                <div class="field">
                    <label>STAT {{ $i + 1 }} — VALUE</label>
                    <input type="text" name="stats[{{ $i }}][value]" value="{{ $stat['value'] }}" placeholder="12+">
                    <label style="margin-top:.6rem;">LABEL</label>
                    <input type="text" name="stats[{{ $i }}][label]" value="{{ $stat['label'] }}" placeholder="Years of Experience">
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">Philosophy</h3>
        @foreach (array_pad(\App\Support\Settings::json('philosophy') ?: \App\Support\Studio::philosophy(), 4, ['title' => '', 'text' => '']) as $i => $item)
            <div class="form-grid">
                <div class="field">
                    <label>ITEM {{ $i + 1 }} — TITLE</label>
                    <input type="text" name="philosophy[{{ $i }}][title]" value="{{ $item['title'] }}" placeholder="LIGHT">
                </div>
                <div class="field">
                    <label>DESCRIPTION</label>
                    <textarea name="philosophy[{{ $i }}][text]" rows="2">{{ $item['text'] }}</textarea>
                </div>
            </div>
        @endforeach
    </div>

    <button class="btn-primary" type="submit">Save About Page</button>
</form>
@endsection
