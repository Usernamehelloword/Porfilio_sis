<?php

namespace App\Http\Controllers;

use App\Support\Content;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class JournalController extends Controller
{
    public function index(): View
    {
        return view('journal.index', [
            'articles' => Content::articles(),
        ]);
    }

    public function show(string $slug): View
    {
        $article = Content::findArticle($slug);

        if ($article === null) {
            throw new NotFoundHttpException('Article not found.');
        }

        return view('journal.show', [
            'article' => $article,
        ]);
    }
}
