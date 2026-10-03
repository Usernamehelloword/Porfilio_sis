<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title', 'short_description', 'full_description', 'image', 'icon',
        'display_order', 'is_published',
    ];

    protected $casts = ['is_published' => 'boolean'];
}
