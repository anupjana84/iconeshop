<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturnMaster extends Model
{
    protected $table = 'sale_return_master';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(SaleReturnDetails::class, 'return_master_id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    
}
