@extends('admin.layout')

@section('title', 'Projects')

@section('page_title', 'Projects')

@section('topbar_actions')
    <a class="btn-primary" href="{{ route('admin.projects.create') }}">+ Add Project</a>
@endsection

@section('content')
<form class="filter-bar" method="GET" action="{{ route('admin.projects.index') }}">
    <input type="text" name="q" placeholder="Search projects…" value="{{ request('q') }}">
    <select name="category">
        <option value="">All categories</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <select name="year">
        <option value="">All years</option>
        @foreach ($years as $year)
            <option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>
        @endforeach
    </select>
    <input type="text" name="location" placeholder="Location…" value="{{ request('location') }}">
    <select name="status">
        <option value="">All statuses</option>
        <option value="published" @selected(request('status') === 'published')">Published</option>
        <option value="draft" @selected(request('status') === 'draft')">Draft</option>
    </select>
    <select name="sort">
        <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest first</option>
        <option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option>
        <option value="name" @selected(request('sort') === 'name')">By name</option>
        <option value="order" @selected(request('sort') === 'order')">Display order</option>
    </select>
    <button class="btn-ghost" type="submit">Filter</button>
</form>

@if (request('sort') === 'order')
    <p class="hint">Drag the rows to change the display order used across the website, then press “Save order”.</p>
@endif

<div class="card">
    <div class="table-wrap">
        <table class="table" data-reorder-list="{{ request('sort') === 'order' ? route('admin.projects.reorder') : '' }}">
            <thead>
                <tr>
                    <th></th>
                    <th>Project</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projects as $project)
                    <tr data-id="{{ $project->id }}">
                        <td class="drag-cell">{{ request('sort') === 'order' ? '⠿' : ($project->display_order ?: '—') }}</td>
                        <td class="cell-name">
                            <a href="{{ route('admin.projects.edit', $project) }}" class="row-media">
                                <img src="{{ $project->cover_image ? \App\Support\Content::imageUrl($project->cover_image) : route('image', ['key' => $project->slug.'-cover']) }}" alt="">
                            </a>
                            <span>
                                <a href="{{ route('admin.projects.edit', $project) }}"><strong>{{ $project->name }}</strong></a>
                                <small>/projects/{{ $project->slug }}</small>
                            </span>
                        </td>
                        <td>{{ $project->category?->name ?? '—' }}</td>
                        <td>{{ $project->location ?? '—' }}</td>
                        <td>{{ $project->year ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $project->is_published ? 'badge-live' : 'badge-draft' }}">{{ $project->is_published ? 'Published' : 'Draft' }}</span>
                        </td>
                        <td>{{ $project->is_featured ? '★ '.$project->featured_order : '—' }}</td>
                        <td class="cell-actions">
                            <a class="act" href="{{ route('admin.projects.edit', $project) }}">Edit</a>
                            <a class="act" href="{{ route('projects.show', $project->slug) }}" target="_blank">View</a>
                            <form method="POST" action="{{ route('admin.projects.duplicate', $project) }}">
                                @csrf
                                <button class="act" type="submit">Duplicate</button>
                            </form>
                            <form method="POST" action="{{ route('admin.projects.toggle-publish', $project) }}">
                                @csrf
                                <button class="act" type="submit">{{ $project->is_published ? 'Draft' : 'Publish' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" data-confirm="Are you sure you want to delete this project?">
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

{{ $projects->links() }}
@endsection
