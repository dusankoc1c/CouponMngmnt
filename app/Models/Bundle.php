<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bundle extends Model
{
    use SoftDeletes, HasFactory;
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
        if (array_key_exists('coupons_count', $this->attributes)) {
            return $this->coupons_count;
        }

        if ($this->relationLoaded('coupons')) {
            return $this->coupons->count();
        }

        return $this->coupons()->count();
    }

    public function getTotalValue(){
        if (array_key_exists('coupons_sum_discount_amount', $this->attributes)) {
            return $this->coupons_sum_discount_amount ?? 0;
        }

        return $this->coupons()->sum('discount_amount');
    }

    public function getName(){
        return $this->name();
    }


}
