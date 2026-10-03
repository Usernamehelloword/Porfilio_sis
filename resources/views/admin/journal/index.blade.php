@extends('admin.layout')

@section('title', 'Journal')
@section('page_title', 'Journal')

@section('topbar_actions')
    <a class="btn-primary" href="{{ route('admin.journal.create') }}">+ Add Article</a>
@endsection

@section('content')
<form class="filter-bar" method="GET" action="{{ route('admin.journal.index') }}">
    <input type="text" name="q" placeholder="Search articles…" value="{{ request('q') }}">
    <select name="status">
        <option value="">All statuses</option>
        <option value="published" @selected(request('status') === 'published')">Published</option>
        <option value="draft" @selected(request('status') === 'draft')">Draft</option>
    </select>
    <button class="btn-ghost" type="submit">Filter</button>
</form>

<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Article</th><th>Category</th><th>Author</th><th>Date</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach ($articles as $article)
                    <tr>
                        <td>
                            <a href="{{ route('admin.journal.edit', $article) }}"><strong>{{ $article->title }}</strong></a>
                            <small>/journal/{{ $article->slug }}</small>
                        </td>
                        <td>{{ $article->category ?? '—' }}</td>
                        <td>{{ $article->author ?? '—' }}</td>
                        <td>{{ $article->published_at?->format('M d, Y') ?? '—' }}</td>
                        <td><span class="badge {{ $article->status === 'published' ? 'badge-live' : 'badge-draft' }}">{{ ucfirst($article->status) }}</span></td>
                        <td class="cell-actions">
                            <a class="act" href="{{ route('admin.journal.edit', $article) }}">Edit</a>
                            <a class="act" href="{{ route('journal.show', $article->slug) }}" target="_blank">Preview</a>
                            <form method="POST" action="{{ route('admin.journal.toggle-publish', $article) }}">
                                @csrf
                                <button class="act" type="submit">{{ $article->status === 'published' ? 'Draft' : 'Publish' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.journal.destroy', $article) }}" data-confirm="Are you sure you want to delete this article?">
                                @csrf
                                @method('DELETE')
                                <button class="act act-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{ $articles->links() }}
@endsection
