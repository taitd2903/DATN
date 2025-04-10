<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'name', 'slug', 'image', 'description', 'content',
        'is_active', 'views', 'seo_title', 'seo_description', 'seo_keywords',
    ];
}
