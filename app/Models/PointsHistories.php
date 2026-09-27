<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointsHistories extends Model
{
    protected $table = 'points_histories';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
