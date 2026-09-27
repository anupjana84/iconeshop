<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelplineNumber extends Model
{
    protected $table = 'helpline_numbers';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'phones'];

    protected $casts = [
        'phones' => 'array',
    ];
}
