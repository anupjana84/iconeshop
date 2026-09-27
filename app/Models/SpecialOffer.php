<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialOffer extends Model
{
    protected $table = 'special_offers';
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'flat_discount' => 'float',
        'percentage_discount' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function freeProduct()
    {
        return $this->belongsTo(Product::class, 'free_product_id');
    }

    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        return $now->gte($this->start_date) && $now->lte($this->end_date);
    }
}
