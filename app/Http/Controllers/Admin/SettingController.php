<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\UploadsMedia;
use App\Http\Controllers\Controller;
use App\Support\Settings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use UploadsMedia;

    public function index(): View
    {
        return view('admin.settings');
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:190'],
            'studio_name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:60'],
            'address' => ['nullable', 'string', 'max:500'],
            'instagram' => ['nullable', 'url', 'max:190'],
            'facebook' => ['nullable', 'url', 'max:190'],
            'linkedin' => ['nullable', 'url', 'max:190'],
            'youtube' => ['nullable', 'url', 'max:190'],
        ]);

        foreach (['logo', 'favicon'] as $field) {
            if ($request->hasFile($field)) {
                $request->validate([$field => ['image', 'max:2048']]);

                $file = $request->file($field);
                $path = $file->store('branding', 'public');
                $data[$field] = $path;
            }
        }

        Settings::set($data, 'general');

        return back()->with('success', 'Website settings saved.');
    }

    public function updateSeo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'seo_title' => ['required', 'string', 'max:190'],
            'meta_description' => ['required', 'string', 'max:1000'],
            'meta_keywords' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->hasFile('og_image')) {
            $request->validate(['og_image' => ['image', 'max:5120']]);
            $data['og_image'] = $this->storeImage($request->file('og_image'), 'seo');
        }

        Settings::set($data, 'seo');

        return back()->with('success', 'SEO settings saved.');
    }
}
