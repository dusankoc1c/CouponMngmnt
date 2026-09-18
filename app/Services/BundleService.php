<?php

namespace App\Services;

use App\Models\Bundle;
use App\Models\Store;
use App\Repositories\Contracts\BundleRepositoryInterface;
use Illuminate\Validation\ValidationException;


class BundleService
{
    public function __construct(
        private BundleRepositoryInterface $bundleRepository,
        private CouponService $couponService,
        private StoreService $storeService,
    ){}

    public function createBundleWithCoupon(Store $store, array $data): Bundle
    {
        $totalNewAmount = 0;

        if(!empty($data['coupons'])){
            foreach ($data['coupons'] as $coupon){
                $totalNewAmount += $coupon['discount_amount'];

                $hasCouponDate = !empty($coupon['expires_at']);
                $hasBundleDate = !empty($bundle['expires_at']);

                if(!$hasBundleDate && !$hasCouponDate){
                    throw ValidationException::withMessages([
                        'expires_at' => 'Datum isteka je obavezan',
                    ]);
                }
            }
        }

        if(!empty($data['tiers'])){
            foreach ($data['tiers'] as $tier){
                $totalNewAmount += ((float)$tier['amount'] * (int)$tier['quantity']);

                $hasTierDate = !empty($tier['expires_at']);
                $hasBundleDate = !empty($data['expires_at']);

                if(!$hasBundleDate && !$hasTierDate){
                    throw ValidationException::withMessages([
                        'expires_at' => 'Datum isteka je obavezan',
                    ]);
                }
            }
        }

        $this->storeService->assertCanAddValue($store, $totalNewAmount);

        $bundle = $this->bundleRepository->create([
            'store_id' => $store->id,
            'name' => $data['name'],
            'description' => $data['description'],
            'expires_at' => $data['expires_at'],
        ]);

        if (!empty($data['coupons'])) {
            foreach ($data['coupons'] as $couponData) {
                $emailAttempted = $this->couponService->createCouponInBundle($bundle, $couponData);

                if ($emailAttempted) {
                    sleep(1);
                }
            }
        }

        if (!empty($data['tiers'])) {
            foreach ($data['tiers'] as $tierData) {
                for ($i = 0; $i < $tierData['quantity']; $i++) {
                    $this->couponService->createTierCoupon($bundle, $tierData['amount'], $tierData['expires_at'] ?? null);
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
