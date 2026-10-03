<?php

namespace App\Http\Controllers;

use App\Support\Content;
use App\Support\Settings;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('home', [
            'projects' => Content::featuredProjects(),
            'articles' => Content::articles(),
            'stats' => Content::stats(),
            'philosophy' => Content::philosophy(),
            'materials' => \App\Support\Studio::materials(),
            'services' => Content::services(),
            'heroImage' => \App\Support\Content::imageUrl(Settings::get('hero_image')) ?: route('image', ['key' => 'hero']),
        ]);
    }

    public function about(): View
    {
        return view('about', [
            'stats' => Content::stats(),
            'philosophy' => Content::philosophy(),
            'projects' => Content::projects(),
            'designers' => Content::designers(),
        ]);
    }

    public function services(): View
    {
        return view('services', [
            'services' => Content::services(),
            'philosophy' => Content::philosophy(),
        ]);
    }

    public function contact(): View
    {
        return view('contact', [
            'types' => \App\Support\Studio::projectTypes(),
            'budgets' => \App\Support\Studio::budgets(),
        ]);
    }
}
