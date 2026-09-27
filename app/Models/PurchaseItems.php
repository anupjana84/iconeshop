<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItems extends Model
{
    protected $table = 'purchase_items';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class,'master_id', 'id' );
    }
}
