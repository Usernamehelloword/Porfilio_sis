@extends('admin.layout')

@section('title', 'Categories')
@section('page_title', 'Categories')

@section('content')
<div class="dash-grid">
    <section class="card">
        <h3 class="card-title">Add Category</h3>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="filter-bar">
            @csrf
            <input type="text" name="name" placeholder="Category name (e.g. Cultural Architecture)" required>
            <button class="btn-primary" type="submit">Add</button>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Name</th><th>Slug</th><th>Projects</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>
                                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="inline-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $category->name }}">
                                    <button class="act" type="submit">Save</button>
                                </form>
                            </td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>{{ $category->projects()->count() }}</td>
                            <td>
                                <span class="badge {{ $category->is_published ? 'badge-live' : 'badge-draft' }}">{{ $category->is_published ? 'Published' : 'Hidden' }}</span>
                            </td>
                            <td class="cell-actions">
                                <form method="POST" action="{{ route('admin.categories.toggle-publish', $category) }}">
                                    @csrf
                                    <button class="act" type="submit">{{ $category->is_published ? 'Hide' : 'Publish' }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-confirm="Are you sure you want to delete this category?">
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
    </section>
</div>
@endsection
