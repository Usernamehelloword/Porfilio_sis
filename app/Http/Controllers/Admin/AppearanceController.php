<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Settings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppearanceController extends Controller
{
    public const FONTS = [
        'Playfair Display' => 'Playfair Display (serif)',
        'Inter' => 'Inter (sans)',
        'Georgia, serif' => 'Georgia (system serif)',
        'Helvetica Neue, Arial, sans-serif' => 'Helvetica / Arial (system sans)',
    ];

    public const BUTTON_STYLES = ['square', 'rounded', 'pill'];

    public const BUTTON_ANIMATIONS = ['none', 'fade', 'slide', 'scale'];

    public function index(): View
    {
        return view('admin.appearance', [
            'fonts' => self::FONTS,
            'buttonStyles' => self::BUTTON_STYLES,
            'buttonAnimations' => self::BUTTON_ANIMATIONS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'color_primary' => ['required', 'string', 'max:9'],
            'color_secondary' => ['required', 'string', 'max:9'],
            'color_accent' => ['required', 'string', 'max:9'],
            'color_background' => ['required', 'string', 'max:9'],
            'color_text' => ['required', 'string', 'max:9'],
            'font_heading' => ['required', 'string', 'max:120'],
            'font_body' => ['required', 'string', 'max:120'],
            'button_style' => ['required', 'in:square,rounded,pill'],
            'button_animation' => ['required', 'in:none,fade,slide,scale'],
        ]);

        Settings::set($data, 'appearance');

        return back()->with('success', 'Appearance settings saved.');
    }
}
