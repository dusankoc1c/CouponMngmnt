<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Store;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class StoreController extends Controller
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
        return view('store.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
        Store::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Store created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        Gate::authorize('workWith', $store);

        $bundles = $store->bundles;
        $totalValue = $bundles->sum(function ($bundle) {
            return $bundle->getTotalValue();
        });

        return view('store.show', [
            'store' => $store,
            'bundles' => $bundles,
            'totalValue' => $totalValue,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {
        Gate::authorize('workWith', $store);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        Gate::authorize('workWith', $store);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $store->update([
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        $bundles = $store->bundles;
        $totalValue = $bundles->sum(function ($bundle) {
            return $bundle->getTotalValue();
        });

        return view('store.show', [
            'store' => $store,
            'bundles' => $bundles,
            'totalValue' => $totalValue,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        Gate::authorize('workWith', $store);

    }

    public function exportCodes(Request $request, Store $store){
        Gate::authorize('workWith', $store);

        $data = $request->validate([
            'bundle_ids' => ['required', 'array', 'min:1'],
            'bundle_ids.*' => ['exists:bundles,id'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
            'amount_min' => ['nullable', 'numeric'],
            'amount_max' => ['nullable', 'numeric'],
        ]);

        $upit = Coupon::whereIn('bundle_id', $data['bundle_ids'])->with('bundle');

        // ------------ FILTERI -------------
        if($request->filled('created_from')){
            $upit->whereDate('created_at', '>=', $request->created_from);
        }
        if($request->filled('created_to')){
            $upit->whereDate('created_at', '<=', $request->created_to);
        }
        if($request->filled('status') && $request->status != 'all'){
            if($request->status == 'used'){
                $upit->where('is_used', true);
            }else{
                $upit->where('is_used', false);
            }
        }
        if($request->filled('amount_min')){
            $upit->where('discount_amount', '>=', $request->amount_min);
        }
        if($request->filled('amount_max')){
            $upit->where('discount_amount', '<=', $request->amount_max);
        }

        $coupons = $upit->get();

        $filename = 'export-codes.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=export-codes.csv',
        ];

        $callback = function () use ($coupons) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Bundle Name', 'Code', 'Receiver Name', 'Receiver Name', 'Amount', 'Send Date', 'Status', 'Created At']);
            foreach ($coupons as $coupon) {

                if($coupon->is_used){
                    $status = 'Used';
                }else{
                    $status = 'Not Used';
                }

                if($coupon->send_date != null){
                    $send_date = $coupon->send_date;
                }else{
                    $send_date = '';
                }

                fputcsv($file, [
                    $coupon->bundle->name,
                    $coupon->code,
                    $coupon->receiver_name,
                    $coupon->receiver_email,
                    $coupon->discount_amount,
                    $send_date,
                    $status,
                    $coupon->created_at->format('Y-m-d'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
