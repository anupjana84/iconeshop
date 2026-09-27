<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalService extends Model
{
    protected $table = 'external_services';

    protected $fillable = [
        'phone',
        'name',
        'address',
        'pincode',
        'product',
        'serial',
        'budget',
        'final_cost',
        'receive_date',
        'vendor',
        'sent_date',
        'back_date',
        'delivery_date',
        'warranty',
        'status',
    ];

    protected $casts = [
        'receive_date'  => 'date',
        'sent_date'     => 'date',
        'back_date'     => 'date',
        'delivery_date' => 'date',
        'budget'        => 'decimal:2',
        'final_cost'    => 'decimal:2',
    ];
}
