<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $fillable = ['email', 'used_at'];
    protected $casts = [
        'used_at'=>'datetime',
    ];
}
