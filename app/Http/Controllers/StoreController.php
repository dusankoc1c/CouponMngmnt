<?php

namespace App\Http\Controllers;

use App\Helpers\CsvExportHelper;
use App\Http\Requests\ExportCodesRequest;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateEmailTemplatesRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Models\Coupon;
use App\Models\Store;
use App\Services\StoreService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(private StoreService $storeService)
    {}
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
    public function store(StoreStoreRequest $request)
    {
        $data = $request->validated();

        $this->storeService->createStore(auth()->user(), $data);

        return redirect()->route('dashboard')->with('success', 'Store created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        Gate::authorize('workWith', $store);

        $bundles = $store->bundles;
        $totalValue = $this->storeService->calculateTotalValue($store);
        $initialEmailTemplate = $this->storeService->getInitialEmailTemplate($store);
        $reminderEmailTemplate = $this->storeService->getReminderEmailTemplate($store);

        return view('store.show', [
            'store' => $store,
            'bundles' => $bundles,
            'totalValue' => $totalValue,
            'initialEmailTemplate' => $initialEmailTemplate,
            'reminderEmailTemplate' => $reminderEmailTemplate,
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
    public function update(UpdateStoreRequest $request, Store $store)
    {
        $data = $request->validated();

        $this->storeService->updateStore($store, $data);

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

    public function exportCodes(ExportCodesRequest $request, Store $store)
    {
        $data = $request->validated();

        return $this->storeService->exportCodesToCsv($store, $data['bundle_ids'], $request);
    }

    public function updateEmailTemplates(UpdateEmailTemplatesRequest $request, Store $store)
    {
        $data = $request->validated();
        $this->storeService->updateEmailTemplate($store, $data);
        return redirect()->route('store.show', $store)->with('success', 'Store email templates updated successfully');
    }
}
