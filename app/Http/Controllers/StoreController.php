<?php

namespace App\Http\Controllers;

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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        Gate::authorize('workWith', $store);

    }
}
