<?php

namespace App\Services;

use App\Helpers\CouponHelper;
use App\Models\Bundle;
use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepositoryInterface;

class CouponService
{
    public function __construct(private CouponRepositoryInterface $couponRepository){}

    public function createCoupon(Bundle $bundle, array $data): Coupon
    {

        $coupon = $this->couponRepository->create([
            'bundle_id' => $bundle->id,
            'code' => CouponHelper::generateCode($bundle->name),
            'discount_amount' => $data['discount_amount'],
            'receiver_name' => $data['receiver_name'],
            'receiver_email' => $data['receiver_email'],
            'send_date' => $data['send_date'] ?? null,
        ]);

        $coupon->sendInititalMail();

        return $coupon;
    }

    public function createCouponInBundle(Bundle $bundle, array $couponData): bool
    {
        $coupon = $this->couponRepository->create($this->buildCouponAttributes($bundle, $couponData));

        return $coupon->sendInititalMail();
    }

    public function createTierCoupon(Bundle $bundle, float $amount): Coupon
    {
        return $this->couponRepository->create([
            'bundle_id' => $bundle->id,
            'code' => CouponHelper::generateCode($bundle->name),
            'discount_amount' => $amount,
            'receiver_name'=>null,
            'receiver_email'=>null,
            'send_date'=>null,
        ]);
    }

    public function updateCoupon(Coupon $coupon, array $data): Coupon
    {
        $coupon = $this->couponRepository->update($coupon, $data);

        $coupon->sendInititalMail();

        return $coupon;
    }

    public function deleteCoupon(Coupon $coupon): void
    {
        $this->couponRepository->delete($coupon);
    }

    public function toggleUsedStatus(Coupon $coupon): Coupon
    {
        $newStatus = !$coupon->is_used;

        if ($newStatus) {
            $usedAt = now();
        } else {
            $usedAt = null;
        }

        $coupon = $this->couponRepository->update($coupon, [
            'is_used' => $newStatus,
            'used_at' => $usedAt,
        ]);

        return $coupon;
    }

    public function unsubscribeCoupon(Coupon $coupon): void
    {
        $this->couponRepository->update($coupon, [
            'subscribed' => false,
        ]);
    }

    private function buildCouponAttributes(Bundle $bundle, array $couponData): array
    {
        return [
            'bundle_id' => $bundle->id,
            'code' => CouponHelper::generateCode($bundle->name),
            'discount_amount' => $couponData['discount_amount'],
            'receiver_name' => $couponData['receiver_name'],
            'receiver_email' => $couponData['receiver_email'],
            'send_date' => $couponData['send_date'] ?? null,
        ];
    }
}
