<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnDetails extends Model
{
    protected $table = 'purchase_return_details';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function returnMaster()
    {
        return $this->belongsTo(PurchaseReturnMaster::class, 'return_master_id', 'id');
    }
    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItems::class, 'purcchaseItem_id', 'id');
    }
}
