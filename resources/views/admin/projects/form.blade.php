@extends('admin.layout')

@section('title', $project->exists ? 'Edit — '.$project->name : 'Add Project')

@section('page_title', $project->exists ? $project->name : 'Add Project')

@section('topbar_actions')
    @if ($project->exists)
        <a class="btn-ghost" href="{{ route('projects.show', $project->slug) }}" target="_blank">Preview ↗</a>
    @endif
    <button class="btn-primary" type="submit" form="project-form">{{ $project->exists ? 'Save Changes' : 'Create Project' }}</button>
@endsection

@section('content')
<form id="project-form" method="POST" action="{{ $project->exists ? route('admin.projects.update', $project) : route('admin.projects.store') }}">
    @csrf
    @if ($project->exists) @method('PUT') @endif

    <div class="card">
        <h3 class="card-title">Basic Information</h3>
        <div class="form-grid">
            <div class="field">
                <label>PROJECT NAME *</label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}" required>
            </div>
            <div class="field">
                <label>PROJECT SLUG *</label>
                <input type="text" name="slug" value="{{ old('slug', $project->slug) }}" required>
            </div>
            <div class="field">
                <label>CATEGORY</label>
                <select name="category_id">
                    <option value="">— None —</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $project->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>LOCATION</label>
                <input type="text" name="location" value="{{ old('location', $project->location) }}" placeholder="Phnom Penh, Cambodia">
            </div>
            <div class="field">
                <label>YEAR</label>
                <input type="text" name="year" value="{{ old('year', $project->year) }}" placeholder="2026">
            </div>
            <div class="field">
                <label>CLIENT</label>
                <input type="text" name="client" value="{{ old('client', $project->client) }}">
            </div>
            <div class="field">
                <label>AREA</label>
                <input type="text" name="area" value="{{ old('area', $project->area) }}" placeholder="420 m²">
            </div>
            <div class="field">
                <label>PROJECT TYPE</label>
                <input type="text" name="type" value="{{ old('type', $project->type) }}" placeholder="Residential">
            </div>
            <div class="field">
                <label>PROJECT STATUS</label>
                <select name="status">
                    @foreach (['Completed', 'In Progress', 'Concept', 'On Hold'] as $option)
                        <option value="{{ $option }}" @selected(old('status', $project->status) === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="field">
            <label>EXCERPT (short summary shown on cards)</label>
            <textarea name="excerpt" rows="2">{{ old('excerpt', $project->excerpt) }}</textarea>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">Project Description</h3>
        <p class="hint">Use the toolbar to format text: bold, italic, headings, lists, links and quotes.</p>
        <div class="field">
            <label>PROJECT OVERVIEW</label>
            <textarea name="overview" class="rich" rows="5">{{ old('overview', $project->overview) }}</textarea>
        </div>
        <div class="field">
            <label>CONCEPT</label>
            <textarea name="concept" class="rich" rows="5">{{ old('concept', $project->concept) }}</textarea>
        </div>
    <div class="card">
        <h3 class="card-title">Project Description (continued)</h3>
        <div class="field">
            <label>MATERIALS (one per line — shown as tags on the project page)</label>
            <textarea name="materials" rows="4" form="project-form">{{ old('materials', $project->materials) }}</textarea>
        </div>
        <div class="field">
            <label>CHALLENGES</label>
            <textarea name="challenges" class="rich" rows="4" form="project-form">{{ old('challenges', $project->challenges) }}</textarea>
        </div>
        <div class="field">
            <label>SOLUTION</label>
            <textarea name="solution" class="rich" rows="4" form="project-form">{{ old('solution', $project->solution) }}</textarea>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">SEO Settings</h3>
        <div class="form-grid">
            <div class="field">
                <label>SEO TITLE</label>
                <input type="text" name="seo_title" form="project-form" value="{{ old('seo_title', $project->seo_title) }}" placeholder="{{ $project->name }} — Modern Residential Architecture">
            </div>
            <div class="field">
                <label>SEO DESCRIPTION</label>
                <textarea name="seo_description" rows="2" form="project-form">{{ old('seo_description', $project->seo_description) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">Publishing</h3>
        <label class="check">
            <input type="checkbox" name="is_published" value="1" form="project-form" @checked(old('is_published', $project->is_published))> Published — visible on the public website
        </label>
        <label class="check">
            <input type="checkbox" name="is_featured" value="1" form="project-form" @checked(old('is_featured', $project->is_featured))> Featured — shown in the homepage “Selected Work” grid
        </label>
    </div>

    <div class="card">
        <h3 class="card-title">Design Approach</h3>
        <div class="field">
            <label>DESIGN APPROACH</label>
            <textarea name="design_approach" class="rich" rows="4" form="project-form">{{ old('design_approach', $project->design_approach) }}</textarea>
        </div>
    </div>
</form>

@if ($project->exists)
    @include('admin.projects.gallery', ['kind' => 'gallery', 'title' => 'Project Gallery', 'kindLabel' => 'images', 'items' => $project->galleryImages()->get()])
    @include('admin.projects.gallery', ['kind' => 'drawing', 'title' => 'Architectural Drawings', 'kindLabel' => 'drawings', 'items' => $project->drawings()->get()])
@endif
@endsection
