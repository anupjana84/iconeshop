<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnMaster extends Model
{
    protected $table = 'purchase_return_master';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(PurchaseReturnDetails::class, 'return_master_id', 'id');
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }
}
