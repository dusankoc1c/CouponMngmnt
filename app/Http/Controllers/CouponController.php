<?php

namespace App\Http\Controllers;

use App\Helpers\CouponHelper;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Bundle;
use App\Models\Coupon;
use App\Services\CouponService;
use Gate;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(private CouponService $couponService){}
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCouponRequest $request, Bundle $bundle)
    {
        $data = $request->validated();
         $this->couponService->createCoupon($bundle, $data);
        return redirect()->route('bundle.show', $bundle)->with('success', 'Kupon je dodat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        return view('coupons.edit', [
            'coupon' => $coupon,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $data = $request->validated();
        $this->couponService->updateCoupon($coupon, $data);
        return redirect()->route('bundle.show', $coupon->bundle)->with('success', 'Kupon je izmenjen.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $bundle = $coupon->bundle;
        $this->couponService->deleteCoupon($coupon);
        return redirect()->route('bundle.show', $bundle)->with('success', 'Kupon je obrisan.');
    }

    public function toggleUsed(Coupon $coupon){
        $this->couponService->toggleUsedStatus($coupon);
        return redirect()->route('bundle.show', $coupon->bundle)->with('success', 'Kupon je izmenjen.');
    }

    public function unsubscribe(Coupon $coupon){
        $this->couponService->unsubscribeCoupon($coupon);
        return view('coupons.unsubscribe');
    }
}
