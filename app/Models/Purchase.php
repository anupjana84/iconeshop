<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function record()
    {
        return $this->hasMany(PurchaseItems::class,'master_id','id');
    }
}
