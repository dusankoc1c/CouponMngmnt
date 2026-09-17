<?php

namespace App\Repositories;

use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepositoryInterface;

class EloquentCouponRepo implements CouponRepositoryInterface
{

    public function create(array $data): Coupon
    {
        return Coupon::create($data);
    }

    public function update(Coupon $coupon, array $data): Coupon
    {
        $coupon->update($data);

        return $coupon;
    }

    public function delete(Coupon $coupon): void
    {
        $coupon->delete();
    }

    public function findById(int $id): ?Coupon
    {
        return Coupon::find($id);
    }
}
