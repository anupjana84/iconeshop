<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InhouseService extends Model
{
    protected $fillable = [
        'phone',
        'name',
        'alt_phone',
        'address',
        'pincode',
        'product',
        'estimate',
        'final_amount',
        'receive_date',
        'delivery_date',
        'warranty',
        'status',
    ];

    protected $casts = [
        'receive_date' => 'date',
        'delivery_date' => 'date',
        'estimate' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];
}