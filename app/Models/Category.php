<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'hsn_code',
        'gst',
        'image',
        'active'
    ];

    public function products(){
        return $this->hasMany(Product::class, 'category_id','id');
    }
}
