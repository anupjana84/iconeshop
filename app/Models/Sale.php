<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id','id');
    }
    public function salesman()
    {
        return $this->belongsTo(User::class, 'sale_by','id');
    }
    public function items()
    {
        return $this->hasMany(SalesItems::class, 'sale_id','id');
    }
    public function payments()
    {
        return $this->hasMany(PaymentMaster::class, 'sale_id','id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id','id');
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturnMaster::class, 'sale_id','id');
    }
}
