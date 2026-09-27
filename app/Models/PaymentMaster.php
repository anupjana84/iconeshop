<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMaster extends Model
{
    protected $table = 'payment_master';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function expenses()
    {
        return $this->belongsTo(Expenses::class, 'expenses_id');
    }
    public function emi()
    {
        return $this->belongsTo(Finance::class, 'emi_company_id');
    }

    protected $casts = [
        'payment_date' => 'datetime',
    ];

}
