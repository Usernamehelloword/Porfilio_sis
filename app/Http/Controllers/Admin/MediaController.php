<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\UploadsMedia;
use App\Http\Controllers\Controller;
use App\Models\MediaItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    use UploadsMedia;

    public function index(Request $request): View
    {
        $query = MediaItem::query()->latest();

        if ($q = trim((string) $request->query('q'))) {
            $query->where('filename', 'like', "%{$q}%");
        }

        return view('admin.media.index', [
            'items' => $query->paginate(24)->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['files.*' => ['required', 'image', 'max:20480']]);

        $count = 0;

        foreach ($request->file('files', []) as $file) {
            $path = $this->storeImage($file, 'media');

            MediaItem::create([
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'mime' => $file->getClientMimeType(),
                'size' => Storage::disk('public')->size($path),
            ]);

            $count++;
        }

        return back()->with('success', $count.' file'.($count === 1 ? '' : 's').' uploaded.');
    }

    public function replace(Request $request, MediaItem $item): RedirectResponse
    {
        $request->validate(['file' => ['required', 'image', 'max:20480']]);

        $file = $request->file('file');
        $path = $this->storeImage($file, 'media');

        Storage::disk('public')->delete($item->path);

        $item->update([
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'size' => Storage::disk('public')->size($path),
        ]);

        return back()->with('success', 'File replaced.');
    }

    public function destroy(MediaItem $item): RedirectResponse
    {
        Storage::disk('public')->delete($item->path);
        $item->delete();

        return back()->with('success', 'File deleted.');
    }
}
