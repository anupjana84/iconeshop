<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GstReport extends Model
{
    use HasFactory;

    protected $table = 'gst_reports';
    
    protected $fillable = [
        'from_date',
        'to_date',
        'file_path'
    ];
}
