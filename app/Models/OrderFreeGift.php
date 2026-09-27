<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderFreeGift extends Model
{
    protected $table = 'order_free_gifts';
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function mainProduct()
    {
        return $this->belongsTo(Product::class, 'main_product_id');
    }

    public function freeProduct()
    {
        return $this->belongsTo(Product::class, 'free_product_id');
    }
}
