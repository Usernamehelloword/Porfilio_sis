<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'category_id', 'slug', 'name', 'location', 'year', 'client', 'area', 'type', 'status',
        'excerpt', 'overview', 'concept', 'design_approach', 'materials', 'challenges', 'solution',
        'cover_image', 'is_featured', 'featured_order', 'is_published', 'display_order',
        'seo_title', 'seo_description',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('display_order');
    }

    public function galleryImages()
    {
        return $this->images()->where('kind', 'gallery');
    }

    public function drawings()
    {
        return $this->images()->where('kind', 'drawing');
    }

    /** Materials stored as newline/comma separated list. */
    public function materialsList(): array
    {
        $raw = trim((string) $this->materials);

        if ($raw === '') {
            return [];
        }

        $items = preg_split('/\r\n|\r|\n|,/', $raw);

        return array_values(array_filter(array_map('trim', $items)));
    }
}
