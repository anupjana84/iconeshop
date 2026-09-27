<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'banks';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'amount'
    ];

    public function payments(){
        return $this->hasMany(PaymentMaster::class, 'bank_id','id');
    }
}
