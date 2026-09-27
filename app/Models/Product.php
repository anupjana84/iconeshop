<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function category(){
        return $this->belongsTo(Category::class, 'category_id','id');
    }
    public function brand(){
        return $this->belongsTo(Brand::class, 'brand_id','id');
    }
    public function details(){
        return $this->belongsTo(ProductDetails::class, 'details_id');
    }
    public function purchase(){
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }
    public function freeProduct(){
        return $this->belongsTo(Product::class, 'free_product_id', 'id');
    }

    public function specialOffer()
    {
        return $this->hasOne(SpecialOffer::class, 'product_id');
    }

}

