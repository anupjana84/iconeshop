<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturnDetails extends Model
{
    protected $table = 'sale_return_details';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function saleReturnMaster()
    {
        return $this->belongsTo(SaleReturnMaster::class, 'return_master_id');
    }

    public function salesItem()
    {
        return $this->belongsTo(SalesItems::class, 'saleItem_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
