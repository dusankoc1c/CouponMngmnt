<?php

namespace App\Helpers;

use App\Models\Coupon;
use Str;

class CouponHelper
{
    public function __construct(){}

    public static function generateCode(string $bundleName): string
    {
        $prefix = strtoupper(substr($bundleName, 0, 2));

        do {
            $randomPart = strtoupper(Str::random(6));
            $code = $prefix . '-' . $randomPart;
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }
}
