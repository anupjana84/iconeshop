<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetails extends Model
{
    protected $table = 'product_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'description',
        'warranty',
        'status',
        'thumbnail_image',
        'image',
        'display',
        'mrp'
    ];
}
