<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Message;
use App\Models\Project;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'projectCount' => Project::count(),
            'publishedCount' => Project::where('is_published', true)->count(),
            'draftCount' => Project::where('is_published', false)->count(),
            'serviceCount' => \App\Models\Service::count(),
            'messageCount' => Message::count(),
            'newMessageCount' => Message::where('status', 'new')->count(),
            'articleCount' => Article::where('status', 'published')->count(),
            'recentProjects' => Project::latest()->take(4)->get(),
            'recentMessages' => Message::latest()->take(4)->get(),
            'recentArticles' => Article::latest()->take(4)->get(),
        ]);
    }
}
