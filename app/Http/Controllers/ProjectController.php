<?php

namespace App\Http\Controllers;

use App\Support\Content;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('projects.index', [
            'projects' => Content::projects(),
        ]);
    }

    public function show(string $slug): View
    {
        $project = Content::findProject($slug);

        if ($project === null) {
            throw new NotFoundHttpException('Project not found.');
        }

        return view('projects.show', [
            'project' => $project,
            'gallery' => Content::projectGallery($project),
            'coverUrl' => Content::projectCoverUrl($project),
            'next' => Content::nextProject($slug),
            'detail' => \App\Models\Project::where('slug', $slug)->first(),
        ]);
    }
}
