<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
