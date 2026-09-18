<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $fillable = ['email', 'value_limit' ,'used_at'];
    protected $casts = [
        'used_at'=>'datetime',
        'value_limit'=>'decimal:2',
    ];
}
