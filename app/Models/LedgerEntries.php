<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntries extends Model
{
    protected $table = 'ledger_entries';
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
}
