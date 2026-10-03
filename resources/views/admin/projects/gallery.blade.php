<div class="card">
    <h3 class="card-title">{{ $title }}</h3>

    <form method="POST" action="{{ route('admin.projects.images.store', $project) }}" enctype="multipart/form-data" class="upload-row">
        @csrf
        <input type="hidden" name="kind" value="{{ $kind }}">
        @if ($kind === 'drawing')
            <input type="text" name="title" placeholder="Drawing title (e.g. Ground floor plan)">
            <select name="drawing_type">
                <option value="plan">Floor plan</option>
                <option value="site">Site plan</option>
                <option value="elevation">Elevation</option>
                <option value="section">Section</option>
                <option value="diagram">Diagram</option>
                <option value="sketch">Sketch</option>
                <option value="construction">Construction drawing</option>
            </select>
        @endif
        <input type="file" name="images[]" multiple accept="image/*">
        <button class="btn-ghost" type="submit">Upload {{ $kindLabel }}</button>
    </form>

    @if ($items->isEmpty())
        <p class="empty">No {{ $kindLabel }} uploaded yet.</p>
    @else
        <p class="hint">Drag images to reorder. The first image is used as the project cover unless another cover is set.</p>
        <ul class="gallery-grid" data-reorder-list="{{ route('admin.projects.images.reorder', $project) }}" data-reorder-extra-kind="{{ $kind }}">
            @foreach ($items as $i => $image)
                <li class="gallery-item {{ $project->cover_image === $image->image ? 'is-cover' : '' }}" data-id="{{ $image->id }}">
                    <span class="grip">⠿</span>
                    <img src="{{ \App\Support\Content::imageUrl($image->image) }}" alt="">
                    <form method="POST" action="{{ route('admin.projects.images.update', [$project, $image]) }}" class="gallery-edit">
                        @csrf
                        @method('PUT')
                        @if ($kind === 'drawing')
                            <input type="text" name="title" placeholder="Title" value="{{ $image->title }}">
                            <select name="drawing_type">
                                @foreach (['plan' => 'Floor plan', 'site' => 'Site plan', 'elevation' => 'Elevation', 'section' => 'Section', 'diagram' => 'Diagram', 'sketch' => 'Sketch', 'construction' => 'Construction drawing'] as $dtKey => $dtLabel)
                                    <option value="{{ $dtKey }}" @selected($image->drawing_type === $dtKey)>{{ $dtLabel }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="description" placeholder="Description" value="{{ $image->description }}">
                        @else
                            <input type="text" name="caption" placeholder="Image caption" value="{{ $image->caption }}">
                        @endif
                        <button class="act" type="submit">Save</button>
                    </form>
                    <div class="gallery-actions">
                        @if ($kind === 'gallery' && $project->cover_image !== $image->image)
                            <form method="POST" action="{{ route('admin.projects.images.cover', [$project, $image]) }}">
                                @csrf
                                <button class="act" type="submit">Set as cover</button>
                            </form>
                        @elseif ($kind === 'gallery')
                            <span class="badge badge-live">Cover</span>
                        @endif
                        <form method="POST" action="{{ route('admin.projects.images.destroy', [$project, $image]) }}" data-confirm="Are you sure you want to delete this image?">
                            @csrf
                            @method('DELETE')
                            <button class="act act-danger" type="submit">Delete</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
