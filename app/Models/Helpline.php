<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Helpline extends Model
{
    protected $table = 'helpline_numbers';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
