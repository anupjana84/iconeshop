<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salesmen extends Model
{
    protected $table = 'salesmen';
    protected $fillable = [
        'code',
        'user_id',
        'point',
        'other_point',
        'address',
        'status'
    ];
     
    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');   
    }
}
