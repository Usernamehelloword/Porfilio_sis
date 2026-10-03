<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\UploadsMedia;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    use UploadsMedia;

    public function index(Request $request): View
    {
        $query = Project::query()->with('category');

        if ($q = trim((string) $request->query('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->query('category'));
        }

        if ($request->filled('year')) {
            $query->where('year', $request->query('year'));
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->query('status') === 'published');
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%'.$request->query('location').'%');
        }

        match ($request->query('sort', 'newest')) {
            'oldest' => $query->orderBy('created_at'),
            'name' => $query->orderBy('name'),
            'order' => $query->orderBy('display_order')->orderBy('id'),
            default => $query->latest(),
        };

        return view('admin.projects.index', [
            'projects' => $query->paginate(20)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'years' => Project::query()->select('year')->distinct()->orderByDesc('year')->pluck('year')->filter()->values(),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Project(['status' => 'Completed', 'is_published' => false]));
    }

    public function store(Request $request): RedirectResponse
    {
        $project = new Project();
        $this->save($project, $request);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', 'Project created. You can now add gallery images and drawings.');
    }

    public function edit(Project $project): View
    {
        return $this->form($project);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->save($project, $request);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function duplicate(Project $project): RedirectResponse
    {
        $copy = $project->replicate();
        $copy->slug = $project->slug.'-copy-'.Str::random(4);
        $copy->name = $project->name.' (Copy)';
        $copy->is_published = false;
        $copy->is_featured = false;
        $copy->save();

        foreach ($project->images as $image) {
            $copy->images()->create($image->only([
                'kind', 'title', 'caption', 'description', 'drawing_type', 'image', 'is_cover', 'display_order',
            ]));
        }

        return redirect()->route('admin.projects.edit', $copy)->with('success', 'Project duplicated as a draft.');
    }

    public function togglePublish(Project $project): RedirectResponse
    {
        $project->is_published = ! $project->is_published;
        $project->save();

        return back()->with('success', $project->is_published ? 'Project published.' : 'Project moved to drafts.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }

    // ------------------------------------------------------------ gallery & drawings

    public function uploadImages(Request $request, Project $project): RedirectResponse
    {
        $request->validate(['images.*' => ['required', 'image', 'max:20480']]);

        $kind = $request->input('kind', 'gallery');
        $order = (int) ($project->images()->where('kind', $kind)->max('display_order') ?? 0);

        foreach ($request->file('images', []) as $file) {
            $project->images()->create([
                'kind' => $kind,
                'image' => $this->storeImage($file, "projects/{$project->slug}"),
                'caption' => $kind === 'gallery'
                    ? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                    : $request->input('title'),
                'drawing_type' => $request->input('drawing_type'),
                'display_order' => ++$order,
            ]);
        }

        // First gallery image automatically becomes the cover unless one exists.
        if ($kind === 'gallery' && ! $project->cover_image) {
            $project->update(['cover_image' => $project->galleryImages()->first()?->image]);
        }

        return back()->with('success', $kind === 'gallery' ? 'Images uploaded.' : 'Drawings uploaded.');
    }

    public function updateImage(Request $request, Project $project, ProjectImage $image): RedirectResponse
    {
        $image->update($request->only(['title', 'caption', 'description', 'drawing_type']));

        return back()->with('success', 'Image updated.');
    }

    public function setCover(Project $project, ProjectImage $image): RedirectResponse
    {
        $project->update(['cover_image' => $image->image]);

        return back()->with('success', 'Cover image updated.');
    }

    public function destroyImage(Project $project, ProjectImage $image): RedirectResponse
    {
        if ($project->cover_image === $image->image) {
            $project->update(['cover_image' => null]);
        }

        Storage::disk('public')->delete($image->image);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    public function reorderImages(Request $request, Project $project): RedirectResponse
    {
        $kind = $request->input('kind', 'gallery');

        foreach (array_values($request->input('ids', [])) as $i => $id) {
            ProjectImage::where('project_id', $project->id)
                ->where('kind', $kind)
                ->where('id', $id)
                ->update(['display_order' => $i + 1]);
        }

        return back()->with('success', 'Order saved.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        foreach (array_values($request->input('ids', [])) as $i => $id) {
            Project::where('id', $id)->update(['display_order' => $i + 1]);
        }

        return back()->with('success', 'Project order saved.');
    }

    // ------------------------------------------------------------ internals

    private function form(Project $project): View
    {
        return view('admin.projects.form', [
            'project' => $project,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    private function save(Project $project, Request $request): void
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'slug' => ['required', 'string', 'max:190', 'alpha_dash'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'location' => ['nullable', 'string', 'max:190'],
            'year' => ['nullable', 'string', 'max:10'],
            'client' => ['nullable', 'string', 'max:190'],
            'area' => ['nullable', 'string', 'max:60'],
            'type' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'string', 'max:60'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'overview' => ['nullable', 'string'],
            'concept' => ['nullable', 'string'],
            'design_approach' => ['nullable', 'string'],
            'materials' => ['nullable', 'string'],
            'challenges' => ['nullable', 'string'],
            'solution' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:500'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($project->is_featured && ! $data['is_featured']) {
            $data['featured_order'] = 0;
        }

        if ($data['is_featured'] && ! $project->is_featured) {
            $data['featured_order'] = (int) Project::max('featured_order') + 1;
        }

        $project->fill($data)->save();
    }
}
