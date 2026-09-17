<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bundle extends Model
{
    use SoftDeletes;
    protected $fillable = ['store_id', 'name', 'description', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    //broj kodova u bundle
    public function numberOfCodes(){
        return $this->coupons->count();
    }

    public function getTotalValue(){
        return $this->coupons->sum('discount_amount');
    }

    public function getName(){
        return $this->name();
    }


}
