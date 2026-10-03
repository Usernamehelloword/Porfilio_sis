<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Designer;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;

/**
 * Single source of truth for everything the public website displays.
 * Reads from the database (managed via the admin panel) and gracefully
 * falls back to the original static definitions when a table is empty.
 */
final class Content
{
    private static array $tablesReady = [];

    /** true when the given database table exists; checked with a per-request cache. */
    public static function tableReady(string $table): bool
    {
        if (! isset(static::$tablesReady[$table])) {
            try {
                static::$tablesReady[$table] = \Illuminate\Support\Facades\Schema::hasTable($table);
            } catch (\Throwable) {
                static::$tablesReady[$table] = false;
            }
        }

        return static::$tablesReady[$table];
    }

    // ---------------------------------------------------------------- projects

    public static function hasProjects(): bool
    {
        return static::tableReady('projects') && Project::count() > 0;
    }

    /** All published projects in the legacy array shape used by the public views. */
    public static function projects(): array
    {
        if (! static::hasProjects()) {
            return Projects::all();
        }

        return Project::query()
            ->where('is_published', true)
            ->orderBy('display_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Project $p) => static::projectArray($p))
            ->all();
    }

    public static function featuredProjects(): array
    {
        if (! static::hasProjects()) {
            return array_slice(static::projects(), 0, 6);
        }

        return Project::query()
            ->where('is_published', true)
            ->where('is_featured', true)
            ->orderBy('featured_order')
            ->get()
            ->map(fn (Project $p) => static::projectArray($p))
            ->all();
    }

    public static function findProject(string $slug): ?array
    {
        if (static::tableReady('projects')) {
            $project = Project::where('slug', $slug)->first();

            if ($project !== null) {
                return static::projectArray($project);
            }
        }

        return Projects::find($slug);
    }

    public static function nextProject(string $slug): ?array
    {
        $all = static::projects();

        foreach ($all as $i => $project) {
            if ($project['slug'] === $slug) {
                return $all[($i + 1) % count($all)];
            }
        }

        return null;
    }

    private static function projectArray(Project $p): array
    {
        return [
            'id' => $p->id,
            'slug' => $p->slug,
            'index' => sprintf('%02d', $p->display_order ?: 99),
            'name' => $p->name,
            'category' => $p->category?->name ?? '',
            'location' => $p->location ?? '',
            'year' => $p->year ?? '',
            'area' => $p->area ?? '',
            'client' => $p->client ?? '',
            'type' => $p->type ?? '',
            'status' => $p->status ?? '',
            'excerpt' => $p->excerpt ?? '',
            'cover' => $p->cover_image ? static::imageUrl($p->cover_image) : '',
            'materials' => $p->materialsList(),
            'seo_title' => $p->seo_title,
            'seo_description' => $p->seo_description,
        ];
    }

    // ---------------------------------------------------------------- helpers

    public static function imageUrl(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        if (str_starts_with($path, 'http') || str_starts_with($path, '/')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    // ---------------------------------------------------------------- gallery

    /** Gallery items for a project page: [src, caption, kind, span]. */
    public static function projectGallery(array $project): array
    {
        $model = static::tableReady('project_images') && static::tableReady('projects')
            ? Project::where('slug', $project['slug'])->first()
            : null;

        if ($model !== null && $model->images()->exists()) {
            return $model->images->map(function ($image, $i) {
                $kind = $image->kind === 'drawing'
                    ? 'drawing'
                    : ($image->drawing_type ?: 'photo');

                $span = $i === 0 ? 'full' : (($i % 3) === 1 ? 'wide' : 'narrow');

                return [
                    static::imageUrl($image->image),
                    $image->caption ?: $image->title,
                    $kind,
                    $span,
                ];
            })->all();
        }

        $n = (int) $project['index'];

        return Projects::gallery($n);
    }

    public static function projectCoverUrl(array $project): string
    {
        if (! empty($project['cover'])) {
            return $project['cover'];
        }

        return route('image', ['key' => $project['slug'].'-cover']);
    }

    // ---------------------------------------------------------------- journal

    public static function hasArticles(): bool
    {
        return static::tableReady('articles') && Article::count() > 0;
    }

    public static function articles(): array
    {
        if (! static::hasArticles()) {
            return Journal::all();
        }

        return Article::published()
            ->get()
            ->map(fn (Article $a) => static::articleArray($a))
            ->all();
    }

    public static function findArticle(string $slug): ?array
    {
        if (static::tableReady('articles')) {
            $article = Article::where('slug', $slug)->first();

            if ($article !== null) {
                return static::articleArray($article);
            }
        }

        return Journal::find($slug);
    }

    private static function articleArray(Article $a): array
    {
        return [
            'id' => $a->id,
            'slug' => $a->slug,
            'category' => $a->category ?? '',
            'date' => $a->published_at?->format('F d, Y') ?? $a->created_at?->format('F d, Y'),
            'title' => $a->title,
            'excerpt' => $a->excerpt ?? '',
            'body' => $a->content ?? '',
            'image' => $a->featured_image ? static::imageUrl($a->featured_image) : '',
            'seo_title' => $a->seo_title,
            'seo_description' => $a->seo_description,
        ];
    }

    // ---------------------------------------------------------------- services

    public static function hasServices(): bool
    {
        return static::tableReady('services') && Service::count() > 0;
    }

    public static function services(): array
    {
        if (! static::hasServices()) {
            return Studio::services();
        }

        return Service::query()
            ->where('is_published', true)
            ->orderBy('display_order')
            ->get()
            ->values()
            ->map(fn (Service $s, $i) => [
                'index' => sprintf('%02d', $i + 1),
                'title' => $s->title,
                'text' => $s->short_description ?? '',
                'full' => $s->full_description ?? '',
                'image' => $s->image ? static::imageUrl($s->image) : '',
                'icon' => $s->icon ?? '',
            ])
            ->all();
    }

    // ---------------------------------------------------------------- designers

    public static function designers(): array
    {
        if (! static::tableReady('designers')) {
            return [];
        }

        return Designer::query()
            ->where('is_published', true)
            ->orderBy('display_order')
            ->get()
            ->values()
            ->map(fn (Designer $d, $i) => [
                'index' => sprintf('%02d', $i + 1),
                'name' => $d->name,
                'position' => $d->position ?? '',
                'bio' => $d->bio ?? '',
                'photo' => $d->photo ? static::imageUrl($d->photo) : '',
                'email' => $d->email ?? '',
                'linkedin' => $d->linkedin ?? '',
                'instagram' => $d->instagram ?? '',
                'years' => $d->years_experience ?? '',
                'specialization' => $d->specialization ?? '',
            ])
            ->all();
    }

    // ---------------------------------------------------------------- about

    public static function stats(): array
    {
        $stats = Settings::json('stats');

        if ($stats !== []) {
            return $stats;
        }

        return Studio::stats();
    }

    public static function philosophy(): array
    {
        $items = Settings::json('philosophy');

        if ($items !== []) {
            return array_map(fn ($i, $item) => [
                'index' => sprintf('%02d', $i + 1),
                'title' => $item['title'] ?? '',
                'text' => $item['text'] ?? '',
            ], array_keys($items), $items);
        }

        return Studio::philosophy();
    }
}

