<?php

namespace App\Http\Controllers;

use App\Helpers\CouponHelper;
use App\Http\Requests\StoreBundleRequest;
use App\Models\Bundle;
use App\Models\Coupon;
use App\Models\Store;
use App\Services\BundleService;
use Gate;
use Illuminate\Http\Request;
use Log;

class BundleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(private BundleService $bundleService){}
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
    public function store(StoreBundleRequest $request, Store $store)
    {

        $data = $request->validated();

        $this->bundleService->createBundleWithCoupon($store, $data);

        return redirect()->route('store.show', $store)->with('success', 'Bundle je uspesno kreiran.');
    }

    public function show(Bundle $bundle)
    {
        Gate::authorize('workWith', $bundle->store);

        $coupons = $bundle->coupons;

        return view('bundles.show', [
            'bundle' => $bundle,
            'coupons' => $coupons,
        ]);
    }

    public function resendAll(Bundle $bundle)
    {
        Gate::authorize('workWith', $bundle->store);
        $result = $this->bundleService->resendAllBundle($bundle);
        $msg = 'posalto : '. $result['sent'] . 'preskoceno ' . $result['skipped'];
        return redirect()->route('bundle.show', $bundle)->with('success', $msg);
    }


    public function destroy(Bundle $bundle)
    {
        Gate::authorize('workWith', $bundle->store);

        $store = $bundle->store;

        $this->bundleService->deleteBundle($bundle);

        return redirect()->route('store.show', $bundle->store)->with('success', 'bundle deleted');
    }
}
