<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'display_order', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
