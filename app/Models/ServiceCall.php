<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCall extends Model
{
    protected $table = 'service_calls';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function remark()
    {
        return $this->belongsTo(ServiceStatus::class, 'call_id', 'id');
    }
    protected $fillable = [
        'name',
        'phone',
        'address',
        'pin',
        'note',
        'voice_note',
        'invoice_image',
        'call_date',
        'call_id',
        'status',

        'bill_date',

        'product_id',
        'sl_no',
        'product_name',
        'serial_no',

        'product_id_2',
        'sl_no_2',
        'product_name_2',
        'serial_no_2',

        'case_id_1',
        'case_id_2',
        'case_id_date',
        'remarks',
    ];
}
