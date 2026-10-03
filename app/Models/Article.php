<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'slug', 'title', 'author', 'category', 'featured_image', 'content', 'excerpt',
        'published_at', 'seo_title', 'seo_description', 'tags', 'status',
    ];

    protected $casts = ['published_at' => 'date'];

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->orderByDesc('published_at');
    }
}
