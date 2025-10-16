<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarouselSlide extends Model
{
    protected $fillable = [
        'title',
        'title_en',
        'description',
        'description_en',
        'image_url',
        'link_url',
        'link_text',
        'link_text_en',
        'sort',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort' => 'integer',
    ];
}
