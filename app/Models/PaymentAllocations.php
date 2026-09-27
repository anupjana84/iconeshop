<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAllocations extends Model
{
    protected $table = 'payment_allocations';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
