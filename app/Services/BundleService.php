<?php

namespace App\Services;

use App\Models\Bundle;
use App\Models\Coupon;
use App\Models\Store;
use App\Repositories\Contracts\BundleRepositoryInterface;
use DB;
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
                $hasBundleDate = !empty($data['expires_at']);

                if (!$hasBundleDate && !$hasCouponDate) {
                    throw ValidationException::withMessages([
                        'expires_at' => __('errors.expires_at_required'),
                    ]);
                }
            }
        }

        if(!empty($data['tiers'])){
            foreach ($data['tiers'] as $tier){
                $totalNewAmount += ((float)$tier['amount'] * (int)$tier['quantity']);

                $hasTierDate = !empty($tier['expires_at']);
                $hasBundleDate = !empty($data['expires_at']);
                if (!$hasBundleDate && !$hasTierDate) {
                    throw ValidationException::withMessages([
                        'expires_at' => __('errors.expires_at_required'),
                    ]);
                }

            }
        }

        $this->storeService->assertCanAddValue($store, $totalNewAmount);

        // ------ TRANSAKCIJA BEZ MEJLA =-----------
        [$bundle, $noviKuponiZaMejl] = DB::transaction(function () use ($store, $data) {
            $bundle = $this->bundleRepository->create([
                'store_id' => $store->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'expires_at' => $data['expires_at'],
            ]);

            $noviKuponiZaMejl = [];

            if (!empty($data['coupons'])) {
                foreach ($data['coupons'] as $couponData) {
                    $coupon = $this->couponService->createCouponRecordOnly($bundle, $couponData);
                    $noviKuponiZaMejl[] = $coupon;
                }
            }

            if (!empty($data['tiers'])) {
                foreach ($data['tiers'] as $tierData) {
                    for ($i = 0; $i < $tierData['quantity']; $i++) {
                        $this->couponService->createTierCoupon($bundle, $tierData['amount'], $tierData['expires_at'] ?? null);
                    }
                }
            }

            return [$bundle, $noviKuponiZaMejl];
        });
        // kraj trans.

        foreach ($noviKuponiZaMejl as $coupon) {
            $emailAtempted = $coupon->sendInititalMail();

            if($emailAtempted){
                sleep(1);
            }
        }

        return $bundle;
    }

    public function resendAllBundle(Bundle $bundle): array
    {
        $coupons = $bundle->coupons;
        $sentCount = 0;
        $skippedCount = 0;

        foreach ($coupons as $coupon) {
            $wasSent = $this->couponService->resendNeededMail($coupon);

            if ($wasSent) {
                $sentCount++;
            } else {
                $skippedCount++;
            }
        }

        return [
            'sent' => $sentCount,
            'skipped' => $skippedCount,
        ];
    }

    public function deleteBundle(Bundle $bundle): void
    {
        DB::transaction(function () use ($bundle) {
            Coupon::where('bundle_id', $bundle->id)->delete();

            $this->bundleRepository->delete($bundle);
        });
    }
}
