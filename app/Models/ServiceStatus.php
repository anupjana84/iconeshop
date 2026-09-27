<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceStatus extends Model
{
    protected $table = 'service_status';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function serviceCall()
    {
        return $this->belongsTo(ServiceCall::class);
    }
}
