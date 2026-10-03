<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\UploadsMedia;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use UploadsMedia;

    public function index(): View
    {
        return view('admin.services.index', [
            'services' => Service::orderBy('display_order')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Service());
    }

    public function store(Request $request): RedirectResponse
    {
        $service = new Service();
        $this->save($service, $request);

        return redirect()->route('admin.services.index')->with('success', 'Service created.');
    }

    public function edit(Service $service): View
    {
        return $this->form($service);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $this->save($service, $request);

        return redirect()->route('admin.services.index')->with('success', 'Service updated.');
    }

    public function togglePublish(Service $service): RedirectResponse
    {
        $service->is_published = ! $service->is_published;
        $service->save();

        return back()->with('success', $service->is_published ? 'Service published.' : 'Service hidden.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        foreach (array_values($request->input('ids', [])) as $i => $id) {
            Service::where('id', $id)->update(['display_order' => $i + 1]);
        }

        return back()->with('success', 'Display order saved.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return back()->with('success', 'Service deleted.');
    }

    private function form(Service $service): View
    {
        return view('admin.services.form', ['service' => $service]);
    }

    private function save(Service $service, Request $request): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'full_description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:60'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published', $service->exists);

        if ($request->hasFile('image')) {
            $request->validate(['image' => ['image', 'max:10240']]);
            $data['image'] = $this->storeImage($request->file('image'), 'services');
        }

        $service->fill($data)->save();
    }
}
