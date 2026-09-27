<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $guarded = [];

    // Define the relationship with the Order model
    public function salesman()
    {
        return $this->belongsTo(Salesmen::class, 'salesman_id');
    }

    public function ledgers()
    {
        return $this->hasMany(LedgerEntries::class, 'company_id');
    }
}
