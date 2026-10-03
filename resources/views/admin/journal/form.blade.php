@extends('admin.layout')

@section('title', $article->exists ? 'Edit — '.$article->title : 'Add Article')
@section('page_title', $article->exists ? $article->title : 'Add Article')

@section('topbar_actions')
    @if ($article->exists)
        <a class="btn-ghost" href="{{ route('journal.show', $article->slug) }}" target="_blank">Preview ↗</a>
    @endif
    <button class="btn-primary" type="submit" form="article-form">{{ $article->exists ? 'Save Changes' : 'Create Article' }}</button>
@endsection

@section('content')
<form id="article-form" method="POST" action="{{ $article->exists ? route('admin.journal.update', $article) : route('admin.journal.store') }}" enctype="multipart/form-data">
    @csrf
    @if ($article->exists) @method('PUT') @endif

    <div class="card">
        <h3 class="card-title">Article</h3>
        <div class="form-grid">
            <div class="field">
                <label>TITLE *</label>
                <input type="text" name="title" value="{{ old('title', $article->title) }}" required>
            </div>
            <div class="field">
                <label>SLUG *</label>
                <input type="text" name="slug" value="{{ old('slug', $article->slug) }}" required>
            </div>
            <div class="field">
                <label>AUTHOR</label>
                <input type="text" name="author" value="{{ old('author', $article->author) }}">
            </div>
            <div class="field">
                <label>CATEGORY</label>
                <input type="text" name="category" value="{{ old('category', $article->category) }}" placeholder="Theory / Materials / Practice / Interiors" list="journal-categories">
                <datalist id="journal-categories">
                    <option value="Theory"><option value="Materials"><option value="Practice"><option value="Interiors">
                </datalist>
            </div>
            <div class="field">
                <label>TAGS (comma separated)</label>
                <input type="text" name="tags" value="{{ old('tags', $article->tags) }}">
            </div>
            <div class="field">
                <label>PUBLISHED DATE</label>
                <input type="date" name="published_at" value="{{ old('published_at', $article->published_at?->format('Y-m-d')) }}">
            </div>
        </div>
        <div class="field">
            <label>EXCERPT</label>
            <textarea name="excerpt" rows="2">{{ old('excerpt', $article->excerpt) }}</textarea>
        </div>
        <div class="field">
            <label>CONTENT</label>
            <textarea name="content" class="rich" rows="14">{{ old('content', $article->content) }}</textarea>
        </div>
        <div class="field">
            <label>FEATURED IMAGE</label>
            @if ($article->featured_image)
                <div class="current-media"><img src="{{ \App\Support\Content::imageUrl($article->featured_image) }}" alt=""> <span>Current image</span></div>
            @endif
            <input type="file" name="featured_image" accept="image/*">
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">SEO</h3>
        <div class="form-grid">
            <div class="field">
                <label>SEO TITLE</label>
                <input type="text" name="seo_title" value="{{ old('seo_title', $article->seo_title) }}">
            </div>
            <div class="field">
                <label>SEO DESCRIPTION</label>
                <textarea name="seo_description" rows="2">{{ old('seo_description', $article->seo_description) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">Status</h3>
        <div class="form-grid">
            <div class="field">
                <label>STATUS</label>
                <select name="status">
                    <option value="draft" @selected(old('status', $article->status) === 'draft')>Draft</option>
                    <option value="published" @selected(old('status', $article->status) === 'published')>Published</option>
                </select>
            </div>
        </div>
    </div>

    <button class="btn-primary" type="submit">{{ $article->exists ? 'Save Changes' : 'Create Article' }}</button>
</form>
@endsection
