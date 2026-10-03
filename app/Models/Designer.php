<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designer extends Model
{
    protected $fillable = [
        'name', 'position', 'photo', 'bio', 'email', 'linkedin', 'instagram',
        'years_experience', 'specialization', 'is_published', 'display_order',
    ];

    protected $casts = ['is_published' => 'boolean'];
}
