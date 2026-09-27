<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesItems extends Model
{
    protected $table = 'sales_items';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function product()
    {
    return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }
}
