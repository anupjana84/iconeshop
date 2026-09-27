<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquary extends Model
{
    protected $table = 'enquary';

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'loc',
        'msg'
    ];
}