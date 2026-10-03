<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\UploadsMedia;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Support\Settings;

class HomepageController extends Controller
{
    use UploadsMedia;

    public function index(): View
    {
        return view('admin.homepage', [
            'featured' => Project::where('is_featured', true)->orderBy('featured_order')->get(),
            'available' => Project::where('is_featured', false)->orderBy('name')->get(),
        ]);
    }

    public function updateHero(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hero_title' => ['required', 'string', 'max:500'],
            'hero_subtitle' => ['nullable', 'string', 'max:190'],
            'hero_description' => ['nullable', 'string', 'max:1000'],
            'hero_button_text' => ['nullable', 'string', 'max:120'],
            'hero_button_link' => ['nullable', 'string', 'max:190'],
            'hero_video' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->hasFile('hero_image')) {
            $request->validate(['hero_image' => ['image', 'max:20480']]);
            $data['hero_image'] = $this->storeImage($request->file('hero_image'), 'homepage');
        }

        Settings::set($data, 'homepage');

        return back()->with('success', 'Hero section updated.');
    }

    public function addFeatured(Request $request): RedirectResponse
    {
        $request->validate(['project_id' => ['required', 'exists:projects,id']]);

        $project = Project::findOrFail($request->input('project_id'));
        $project->update([
            'is_featured' => true,
            'featured_order' => (int) Project::max('featured_order') + 1,
        ]);

        return back()->with('success', $project->name.' added to featured projects.');
    }

    public function removeFeatured(Project $project): RedirectResponse
    {
        $project->update(['is_featured' => false, 'featured_order' => 0]);

        return back()->with('success', $project->name.' removed from featured projects.');
    }

    public function togglePublish(Project $project): RedirectResponse
    {
        $project->is_published = ! $project->is_published;
        $project->save();

        return back()->with('success', $project->is_published ? 'Project published.' : 'Project unpublished.');
    }

    public function reorderFeatured(Request $request): RedirectResponse
    {
        foreach (array_values($request->input('ids', [])) as $i => $id) {
            Project::where('id', $id)->update(['featured_order' => $i + 1]);
        }

        return back()->with('success', 'Featured order saved.');
    }
}
