<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['title', 'price', 'description', 'category', 'image', 'rating'];

    protected $casts = [
        'rating' => 'array',
        'price' => 'decimal:2'
    ];
}
