<?php

namespace App\Services;

use App\Models\Bundle;
use App\Models\Store;
use App\Repositories\Contracts\BundleRepositoryInterface;
use App\Repositories\Contracts\CouponRepositoryInterface;

class BundleService
{
    public function __construct(
        private BundleRepositoryInterface $bundleRepository,
        private CouponService $couponService){}

    public function createBundleWithCoupon(Store $store, array $data): Bundle
    {
        $bundle = $this->bundleRepository->create([
            'store_id' => $store->id,
            'name' => $data['name'],
            'description' => $data['description'],
            'expires_at' => $data['expires_at'],
        ]);

        if(!empty($data['coupons'])){
            foreach ($data['coupons'] as $coupon){
                $emailAtempt = $this->couponService->createCouponInBundle($bundle, $coupon);
                if($emailAtempt){
                    sleep(1);
                }
            }
        }

        if(!empty($data['tiers'])){
            foreach ($data['tiers'] as $tier){
                for($i = 0; $i < $tier['quantity']; $i++){
                    $this->couponService->createTierCoupon($bundle, $tier['amount']);
                }
            }
        }

        return $bundle;
    }

    public function deleteBundle(Bundle $bundle): void
    {
        $this->bundleRepository->delete($bundle);
    }
}
