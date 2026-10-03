@extends('admin.layout')

@section('title', 'Homepage')
@section('page_title', 'Homepage Settings')

@section('content')
<div class="card">
    <h3 class="card-title">Hero Section</h3>
    <form method="POST" action="{{ route('admin.homepage.hero') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <div class="field">
                <label>HERO TITLE (HTML allowed, e.g. &lt;em&gt; for italics)</label>
                <input type="text" name="hero_title" value="{{ old('hero_title', \App\Support\Settings::get('hero_title')) }}">
            </div>
            <div class="field">
                <label>SUBTITLE</label>
                <input type="text" name="hero_subtitle" value="{{ old('hero_subtitle', \App\Support\Settings::get('hero_subtitle')) }}">
            </div>
        </div>
        <div class="field">
            <label>DESCRIPTION</label>
            <textarea name="hero_description" rows="2">{{ old('hero_description', \App\Support\Settings::get('hero_description')) }}</textarea>
        </div>
        <div class="form-grid">
            <div class="field">
                <label>BUTTON TEXT</label>
                <input type="text" name="hero_button_text" value="{{ old('hero_button_text', \App\Support\Settings::get('hero_button_text')) }}">
            </div>
            <div class="field">
                <label>BUTTON LINK</label>
                <input type="text" name="hero_button_link" value="{{ old('hero_button_link', \App\Support\Settings::get('hero_button_link')) }}" placeholder="/projects">
            </div>
        </div>
        <div class="form-grid">
            <div class="field">
                <label>HERO IMAGE</label>
                @if (\App\Support\Settings::get('hero_image'))
                    <div class="current-media"><img src="{{ \App\Support\Content::imageUrl(\App\Support\Settings::get('hero_image')) }}" alt=""> <span>Current image</span></div>
                @endif
                <input type="file" name="hero_image" accept="image/*">
            </div>
            <div class="field">
                <label>HERO VIDEO (URL or path — video overrides the image when set)</label>
                <input type="text" name="hero_video" value="{{ old('hero_video', \App\Support\Settings::get('hero_video')) }}" placeholder="/video/hero.mp4">
            </div>
        </div>
        <button class="btn-primary" type="submit">Save Hero Section</button>
    </form>
</div>

<div class="card">
    <header class="card-head">
        <h3 class="card-title">Featured Projects</h3>
        <form method="POST" action="{{ route('admin.homepage.featured.add') }}" class="inline-form">
            @csrf
            <select name="project_id" required>
                <option value="">— Select a project —</option>
                @foreach ($available as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
            <button class="btn-primary" type="submit">+ Add</button>
        </form>
    </header>

    <p class="hint">Drag to change the order shown on the homepage. 01 is shown first.</p>

    <div class="table-wrap">
        <table class="table" data-reorder-list="{{ route('admin.homepage.featured.reorder') }}">
            <thead>
                <tr><th></th><th>#</th><th>Project</th><th>Status</th><th>Order</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach ($featured as $i => $project)
                    <tr data-id="{{ $project->id }}">
                        <td class="drag-cell">⠿</td>
                        <td>{{ sprintf('%02d', $i + 1) }}</td>
                        <td><strong>{{ $project->name }}</strong> <small>{{ $project->category?->name }}</small></td>
                        <td>
                            <span class="badge {{ $project->is_published ? 'badge-live' : 'badge-draft' }}">{{ $project->is_published ? 'Published' : 'Draft' }}</span>
                        </td>
                        <td>{{ $project->featured_order }}</td>
                        <td class="cell-actions">
                            <form method="POST" action="{{ route('admin.homepage.featured.toggle-publish', $project) }}">
                                @csrf
                                <button class="act" type="submit">{{ $project->is_published ? 'Unpublish' : 'Publish' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.homepage.featured.remove', $project) }}">
                                @csrf
                                <button class="act act-danger" type="submit">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
