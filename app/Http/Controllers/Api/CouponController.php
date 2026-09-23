<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportCodesRequest;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Bundle;
use App\Models\Coupon;
use App\Services\CouponService;
use Gate;

class CouponController extends Controller
{
    public function __construct(private CouponService $couponService){}

    public function index(Bundle $bundle){
        Gate::authorize('workWith', $bundle->store());
        return CouponResource::collection($bundle->coupons);
    }

    public function show(Coupon $coupon){
        Gate::authorize('workWith', $coupon->bundle->store);
        return new CouponResource($coupon);
    }

    public function store(StoreCouponRequest $request, Bundle $bundle){
        $data = $request->validated();
        $coupon = $this->couponService->createCoupon($bundle, $data);
        return new CouponResource($coupon);
    }

    public function update(Coupon $coupon, UpdateCouponRequest $request){
        Gate::authorize('workWith', $coupon->bundle->store);
        $data = $request->validated();
        $coupon = $this->couponService->updateCoupon($coupon, $data);
        return new CouponResource($coupon);
    }

    public function destroy(Coupon $coupon){
        Gate::authorize('workWith', $coupon->bundle->store);
        $this->couponService->deleteCoupon($coupon);
        return response()->json([
            'message' => 'Coupon deleted successfully'
        ]);
    }

    public function resendInitial(Coupon $coupon)
    {
        Gate::authorize('workWith', $coupon->bundle->store);
        $this->couponService->resendInitialMail($coupon);
        return response()->json([
            'message' => 'Poslati su pocetni mejlovi.'
        ]);
    }

    public function resendReminder(Coupon $coupon)
    {
        Gate::authorize('workWith', $coupon->bundle->store);
        $this->couponService->resendReminderMail($coupon);
        return response()->json([
            'message' => 'Poslati su resend mejlovi.'
        ]);
    }

    public function importCodes(ImportCodesRequest $request, Bundle $bundle)
    {
        $file = $request->file('csv_file');
        $result = $this->couponService->importCsv($bundle, $file);

        return response()->json([
            'message' => 'Imported : ' . $result['importedCount'] . ' resteored : ' . $result['restoredCount'] . ' skipped' . $result['skippedCount'],
            'imported' => $result['importedCount'],
            'restored' => $result['restoredCount'],
            'skipped' => $result['skippedCount']
        ]);
    }

    public function toogleUsed(Coupon $coupon)
    {
        Gate::authorize('workWith', $coupon->bundle->store);

        $coupon=$this->couponService->toggleUsedStatus($coupon);

        return new CouponResource($coupon);
    }

    public function unsubscribe(Coupon $coupon)
    {
        $this->couponService->unsubscribeCoupon($coupon);
        return response()->json([
            'message' => 'Coupon unsubscribed successfully'
        ]);
    }
}
