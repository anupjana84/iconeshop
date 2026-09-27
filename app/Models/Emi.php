<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emi extends Model
{
    protected $table = 'emi';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function financeCompany()
    {
        return $this->belongsTo(Finance::class, 'emi_id');
    }
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sales_id');
    }
}
