<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\Coupon;
use App\Models\Store;
use Illuminate\Http\Request;

class BundleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
    public function store(Request $request, Store $store)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
            'coupons' => 'nullable|array',
            'coupons.*.receiver_name' => 'required_with:coupons|string|max:255',
            'coupons.*.receiver_email' => 'required_with:coupons|email|max:255',
            'coupons.*.discount_amount' => 'required_with:coupons|numeric|min:0',
            'coupons.*.send_date' => 'nullable|date',
        ]);

        $bundle = Bundle::create([
            'store_id' => $store->id,
            'name' => $data['name'],
            'description' => $data['description'],
            'expires_at' => $data['expires_at'],
        ]);

        if (!empty($data['coupons'])) {
            foreach ($data['coupons'] as $couponData) {
                Coupon::create([
                    'bundle_id' => $bundle->id,
                    'code' => Coupon::generateCode($bundle->name),
                    'discount_amount' => $couponData['discount_amount'],
                    'receiver_name' => $couponData['receiver_name'],
                    'receiver_email' => $couponData['receiver_email'],
                    'send_date' => $couponData['send_date'] ?? null,
                ]);
            }
        }

        return redirect()->route('store.show', $store)->with('success', 'Bundle je uspesno kreiran.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bundle $bundle)
    {
        $coupons = $bundle->coupons;
        return view('bundles.show', [
            'bundle' => $bundle,
            'coupons' => $coupons,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bundle $bundle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bundle $bundle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bundle $bundle)
    {
        $bundle->delete();

        return redirect()->route('store.show', $bundle->store)->with('success', 'bundle deleted');
    }
}
