<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\UploadsMedia;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    use UploadsMedia;

    public function index(Request $request): View
    {
        $query = Article::query()->latest();

        if ($q = trim((string) $request->query('q'))) {
            $query->where(fn ($w) => $w->where('title', 'like', "%{$q}%")
                ->orWhere('category', 'like', "%{$q}%")
                ->orWhere('author', 'like', "%{$q}%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return view('admin.journal.index', ['articles' => $query->paginate(20)->withQueryString()]);
    }

    public function create(): View
    {
        return $this->form(new Article(['status' => 'draft']));
    }

    public function store(Request $request): RedirectResponse
    {
        $article = new Article();
        $this->save($article, $request);

        return redirect()
            ->route('admin.journal.edit', $article)
            ->with('success', 'Article saved as '.$article->status.'.');
    }

    public function edit(Article $article): View
    {
        return $this->form($article);
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $this->save($article, $request);

        return redirect()->route('admin.journal.edit', $article)->with('success', 'Article updated.');
    }

    public function togglePublish(Article $article): RedirectResponse
    {
        $article->status = $article->status === 'published' ? 'draft' : 'published';

        if ($article->status === 'published' && $article->published_at === null) {
            $article->published_at = now();
        }

        $article->save();

        return back()->with('success', $article->status === 'published' ? 'Article published.' : 'Article moved to drafts.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('admin.journal.index')->with('success', 'Article deleted.');
    }

    private function form(Article $article): View
    {
        return view('admin.journal.form', ['article' => $article]);
    }

    private function save(Article $article, Request $request): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'slug' => ['required', 'string', 'max:190', 'alpha_dash'],
            'author' => ['nullable', 'string', 'max:190'],
            'category' => ['nullable', 'string', 'max:120'],
            'content' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'tags' => ['nullable', 'string', 'max:190'],
            'status' => ['required', 'in:draft,published'],
        ]);

        if ($data['status'] === 'published' && $article->published_at === null && empty($data['published_at'])) {
            $data['published_at'] = now()->toDateString();
        }

        if ($request->hasFile('featured_image')) {
            $request->validate(['featured_image' => ['image', 'max:10240']]);
            $data['featured_image'] = $this->storeImage($request->file('featured_image'), 'journal');
        }

        $article->fill($data)->save();
    }
}
