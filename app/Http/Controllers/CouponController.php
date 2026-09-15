<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Mail\MyEmail;
use Illuminate\Support\Facades\Mail;

class CouponController extends Controller
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
    public function store(Request $request, Bundle $bundle)
    {
        $data = $request->validate([
            'receiver_name' => 'required|string|max:255',
            'receiver_email' => 'required|email|max:255',
            'discount_amount' => 'required|numeric|min:0',
            'send_date' => 'nullable|date',
        ]);

        $coupon = Coupon::create([
            'bundle_id' => $bundle->id,
            'code' => Coupon::generateCode($bundle->name),
            'discount_amount' => $data['discount_amount'],
            'receiver_name' => $data['receiver_name'],
            'receiver_email' => $data['receiver_email'],
            'send_date' => $data['send_date'] ?? null,
        ]);

        if ($coupon->receiver_email != null && $coupon->send_date == null) {
            Mail::to($coupon->receiver_email)->send(new MyEmail($coupon));
            $coupon->email_sent_at = now();
            $coupon->save();
        }
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
    public function update(Request $request, Coupon $coupon)
    {

        $data = $request->validate([
            'receiver_name' => 'required|string|max:255',
            'receiver_email' => 'required|email|max:255',
            'discount_amount' => 'required|numeric|min:0',
            'send_date' => 'nullable|date',
        ]);

        $coupon->update($data);

        return redirect()->route('bundle.show', $coupon->bundle)->with('success', 'Kupon je izmenjen.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $bundle = $coupon->bundle;
        $coupon->delete();

        return redirect()->route('bundle.show', $bundle)->with('success', 'Kupon je obrisan.');
    }

    public function toggleUsed(Coupon $coupon){
        $coupon->is_used = !$coupon->is_used;

        if($coupon->is_used){
            $coupon->used_at = now();
        }else{
            $coupon->used_at = null;
        }

        $coupon->save();

        return redirect()->route('bundle.show', $coupon->bundle)->with('success', 'Kupon je izmenjen.');
    }

    public function unsubscribe(Coupon $coupon){
        $coupon->subscribed = false;
        $coupon->save();

        return view('coupons.unsubscribe');
    }
}
