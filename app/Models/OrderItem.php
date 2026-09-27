<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'orders_items';
    protected $primaryKey = 'id';
    protected $guarded=[];
    // Define the relationship with the Order model
    public function order(){
        return $this->belongsTo(Order::class,'order_id','id');
    }
    // Define the relationship with the Product model
    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }
}
