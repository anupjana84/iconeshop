<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $guarded = [];
    // Define the relationship with the Customer model
    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id','id');
    }
    // Define the relationship with the Purchase model

    public function orderItems(){
        return $this->hasMany(OrderItem::class,'order_id','id');
    }
    public function sale(){
        return $this->hasMany(Sale::class,'order_id','id');
    }
    public function dealer(){
        return $this->belongsTo(User::class,'salesman_id','id');
    }

    public function freeGifts(){
        return $this->hasMany(OrderFreeGift::class, 'order_id');
    }

    
}
