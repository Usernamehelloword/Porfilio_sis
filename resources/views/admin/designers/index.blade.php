@extends('admin.layout')

@section('title', 'Designers')
@section('page_title', 'Designers')

@section('topbar_actions')
    <a class="btn-primary" href="{{ route('admin.designers.create') }}">+ Add Designer</a>
@endsection

@section('content')
<form class="filter-bar" method="GET" action="{{ route('admin.designers.index') }}">
    <input type="text" name="q" placeholder="Search designers…" value="{{ request('q') }}">
    <button class="btn-ghost" type="submit">Search</button>
</form>

<div class="card">
    <div class="table-wrap">
        <table class="table" data-reorder-list="{{ route('admin.designers.reorder') }}">
            <thead>
                <tr><th></th><th>Designer</th><th>Position</th><th>Specialization</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach ($designers as $designer)
                    <tr data-id="{{ $designer->id }}">
                        <td class="drag-cell">⠿ {{ $designer->display_order }}</td>
                        <td class="cell-name">
                            @if ($designer->photo)
                                <a class="row-media" href="{{ route('admin.designers.edit', $designer) }}"><img src="{{ \App\Support\Content::imageUrl($designer->photo) }}" alt=""></a>
                            @else
                                <span class="row-avatar">{{ strtoupper(substr($designer->name, 0, 1)) }}</span>
                            @endif
                            <span>
                                <a href="{{ route('admin.designers.edit', $designer) }}"><strong>{{ $designer->name }}</strong></a>
                                <small>{{ $designer->email }}</small>
                            </span>
                        </td>
                        <td>{{ $designer->position }}</td>
                        <td>{{ Str::limit($designer->specialization, 40) }}</td>
                        <td><span class="badge {{ $designer->is_published ? 'badge-live' : 'badge-draft' }}">{{ $designer->is_published ? 'Published' : 'Hidden' }}</span></td>
                        <td class="cell-actions">
                            <a class="act" href="{{ route('admin.designers.edit', $designer) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.designers.toggle-publish', $designer) }}">
                                @csrf
                                <button class="act" type="submit">{{ $designer->is_published ? 'Unpublish' : 'Publish' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.designers.destroy', $designer) }}" data-confirm="Are you sure you want to delete this designer?">
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
@endsection
