<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectImage extends Model
{
    protected $fillable = [
        'project_id', 'kind', 'title', 'caption', 'description', 'drawing_type',
        'image', 'is_cover', 'display_order',
    ];

    protected $casts = ['is_cover' => 'boolean'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
