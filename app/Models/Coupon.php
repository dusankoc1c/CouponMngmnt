<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Str;

class Coupon extends Model
{
    protected $fillable = [
        'bundle_id', 'code', 'discount_amount',
        'receiver_name', 'receiver_email',
        'send_date', 'send_immediately',
        'is_used', 'used_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'send_immediately' => 'boolean',
        'used_at' => 'datetime',
        'send_date' => 'datetime',
        'discount_amount' => 'decimal:2',
    ];
    public function bundle(): belongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    public static function generateCode(string $bundleName): string
    {
        $prefix = strtoupper(substr($bundleName, 0, 2));

        do {
            $randomPart = strtoupper(Str::random(6));
            $code = $prefix . '-' . $randomPart;
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
