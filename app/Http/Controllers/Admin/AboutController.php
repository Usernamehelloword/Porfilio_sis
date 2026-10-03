<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\UploadsMedia;
use App\Http\Controllers\Controller;
use App\Support\Settings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    use UploadsMedia;

    public function index(): View
    {
        return view('admin.about');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'about_title' => ['required', 'string', 'max:500'],
            'about_description' => ['required', 'string', 'max:2000'],
            'about_quote' => ['nullable', 'string', 'max:1000'],
            'about_body_1' => ['nullable', 'string', 'max:5000'],
            'about_body_2' => ['nullable', 'string', 'max:5000'],
            'portrait_caption' => ['nullable', 'string', 'max:190'],
        ]);

        if ($request->hasFile('portrait_image')) {
            $request->validate(['portrait_image' => ['image', 'max:10240']]);
            $data['portrait_image'] = $this->storeImage($request->file('portrait_image'), 'about');
        }

        // Stats rows: value/label pairs.
        $stats = [];
        foreach ((array) $request->input('stats', []) as $row) {
            $value = trim($row['value'] ?? '');
            $label = trim($row['label'] ?? '');

            if ($value !== '' || $label !== '') {
                $stats[] = ['value' => $value, 'label' => $label];
            }
        }

        $data['stats'] = json_encode($stats, JSON_UNESCAPED_UNICODE);

        // Philosophy rows: title/text pairs.
        $philosophy = [];
        foreach ((array) $request->input('philosophy', []) as $row) {
            $title = trim($row['title'] ?? '');
            $text = trim($row['text'] ?? '');

            if ($title !== '' || $text !== '') {
                $philosophy[] = ['title' => $title, 'text' => $text];
            }
        }

        $data['philosophy'] = json_encode($philosophy, JSON_UNESCAPED_UNICODE);

        Settings::set($data, 'about');

        return back()->with('success', 'About page updated.');
    }
}
