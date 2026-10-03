@extends('admin.layout')

@section('title', 'Media Library')
@section('page_title', 'Media Library')

@section('content')
<div class="card">
    <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="upload-row" id="media-upload">
        @csrf
        <input type="file" name="files[]" multiple accept="image/*">
        <button class="btn-ghost" type="submit">Upload</button>
        <span class="hint">Drag & drop files onto this area — or click browse. Large images are optimized automatically.</span>
    </form>

    <form class="filter-bar" method="GET" action="{{ route('admin.media.index') }}">
        <input type="text" name="q" placeholder="Search files…" value="{{ request('q') }}">
        <button class="btn-ghost" type="submit">Search</button>
    </form>

    <div class="media-grid">
        @forelse ($items as $item)
            <figure class="media-card" data-url="{{ $item->url }}">
                <img src="{{ $item->url }}" alt="{{ $item->filename }}" loading="lazy">
                <figcaption>
                    <strong title="{{ $item->filename }}">{{ Str::limit($item->filename, 22) }}</strong>
                    <small>{{ $item->mime }} · {{ $item->humanSize() }} · {{ $item->created_at->format('M d, Y') }}</small>
                    <span class="media-actions">
                        <button class="act" type="button" data-copy-url>Copy URL</button>
                        <label class="act">Replace
                            <form method="POST" action="{{ route('admin.media.replace', $item) }}" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="file" accept="image/*" hidden data-autosubmit>
                            </form>
                        </label>
                        <form method="POST" action="{{ route('admin.media.destroy', $item) }}" data-confirm="Are you sure you want to delete this file?">
                            @csrf
                            @method('DELETE')
                            <button class="act act-danger" type="submit">Delete</button>
                        </form>
                    </span>
                </figcaption>
            </figure>
        @empty
            <p class="empty">No media yet — upload your first images above.</p>
        @endforelse
    </div>
</div>

{{ $items->links() }}
@endsection
