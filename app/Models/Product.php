<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, Filterable;

    protected $fillable = ['title', 'price', 'description', 'category', 'image', 'rating', 'external_source', 'external_id'];

    protected $casts = [
        'rating' => 'array',
        'price' => 'float'
    ];
}
