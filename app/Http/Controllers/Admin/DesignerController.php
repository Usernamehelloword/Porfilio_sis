<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\UploadsMedia;
use App\Http\Controllers\Controller;
use App\Models\Designer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DesignerController extends Controller
{
    use UploadsMedia;

    public function index(Request $request): View
    {
        $query = Designer::query()->orderBy('display_order')->orderBy('id');

        if ($q = trim((string) $request->query('q'))) {
            $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")
                ->orWhere('position', 'like', "%{$q}%")
                ->orWhere('specialization', 'like', "%{$q}%"));
        }

        return view('admin.designers.index', ['designers' => $query->get()]);
    }

    public function create(): View
    {
        return $this->form(new Designer());
    }

    public function store(Request $request): RedirectResponse
    {
        $designer = new Designer();
        $this->save($designer, $request);

        return redirect()->route('admin.designers.index')->with('success', 'Designer added.');
    }

    public function edit(Designer $designer): View
    {
        return $this->form($designer);
    }

    public function update(Request $request, Designer $designer): RedirectResponse
    {
        $this->save($designer, $request);

        return redirect()->route('admin.designers.index')->with('success', 'Designer updated.');
    }

    public function togglePublish(Designer $designer): RedirectResponse
    {
        $designer->is_published = ! $designer->is_published;
        $designer->save();

        return back()->with('success', $designer->is_published ? 'Designer published.' : 'Designer hidden.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        foreach (array_values($request->input('ids', [])) as $i => $id) {
            Designer::where('id', $id)->update(['display_order' => $i + 1]);
        }

        return back()->with('success', 'Display order saved.');
    }

    public function destroy(Designer $designer): RedirectResponse
    {
        $designer->delete();

        return back()->with('success', 'Designer deleted.');
    }

    private function form(Designer $designer): View
    {
        return view('admin.designers.form', ['designer' => $designer]);
    }

    private function save(Designer $designer, Request $request): void
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'position' => ['nullable', 'string', 'max:190'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'email' => ['nullable', 'email', 'max:190'],
            'linkedin' => ['nullable', 'url', 'max:190'],
            'instagram' => ['nullable', 'url', 'max:190'],
            'years_experience' => ['nullable', 'string', 'max:30'],
            'specialization' => ['nullable', 'string', 'max:190'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published', $designer->exists);

        if ($request->hasFile('photo')) {
            $request->validate(['photo' => ['image', 'max:10240']]);
            $data['photo'] = $this->storeImage($request->file('photo'), 'designers');
        }

        $designer->fill($data)->save();
    }
}
