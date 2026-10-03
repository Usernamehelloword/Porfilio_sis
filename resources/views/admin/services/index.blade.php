@extends('admin.layout')

@section('title', 'Services')
@section('page_title', 'Services')

@section('topbar_actions')
    <a class="btn-primary" href="{{ route('admin.services.create') }}">+ Add Service</a>
@endsection

@section('content')
<div class="card">
    <div class="table-wrap">
        <table class="table" data-reorder-list="{{ route('admin.services.reorder') }}">
            <thead>
                <tr><th></th><th>Service</th><th>Description</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach ($services as $service)
                    <tr data-id="{{ $service->id }}">
                        <td class="drag-cell">⠿ {{ $service->display_order }}</td>
                        <td><a href="{{ route('admin.services.edit', $service) }}"><strong>{{ $service->title }}</strong></a></td>
                        <td>{{ Str::limit($service->short_description, 80) }}</td>
                        <td><span class="badge {{ $service->is_published ? 'badge-live' : 'badge-draft' }}">{{ $service->is_published ? 'Published' : 'Hidden' }}</span></td>
                        <td class="cell-actions">
                            <a class="act" href="{{ route('admin.services.edit', $service) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.services.toggle-publish', $service) }}">
                                @csrf
                                <button class="act" type="submit">{{ $service->is_published ? 'Unpublish' : 'Publish' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.services.destroy', $service) }}" data-confirm="Are you sure you want to delete this service?">
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
