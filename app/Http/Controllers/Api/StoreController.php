<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ExportCodesRequest;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateEmailTemplatesRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Services\StoreService;
use Gate;

class StoreController
{
    public function __construct(private StoreService $storeService){}

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {
            $stores = Store::whereHas('user')->get();
        } else {
            $stores = $user->stores;
        }

        return StoreResource::collection($stores);
    }

    public function store(StoreStoreRequest $request)
    {
        $data = $request->validated();

        $store = $this->storeService->createStore(auth()->user(), $data);

        return new StoreResource($store);
    }

    public function show(Store $store)
    {
        Gate::authorize('workWith', $store);

        return new StoreResource($store);
    }

    public function update(UpdateStoreRequest $request, Store $store)
    {
        $data = $request->validated();

        $store = $this->storeService->updateStore($store, $data);

        return new StoreResource($store);
    }

    public function updateEmailTemplate(UpdateEmailTemplatesRequest $request, Store $store)
    {
        $data = $request->validated();

        $store = $this->storeService->updateEmailTemplate($store, $data);

        return response()->json([
            'message' => 'Email template updated successfully',
        ]);
    }

    public function exportCodes(ExportCodesRequest $request, Store $store)
    {
        $data = $request->validated();

        return $this->storeService->exportCodesToCsv($store, $data['bundle_ids'], $request);
    }
}
