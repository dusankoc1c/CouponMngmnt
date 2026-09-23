<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBundleRequest;
use App\Http\Resources\BundleResource;
use App\Models\Bundle;
use App\Models\Store;
use App\Services\BundleService;
use Illuminate\Support\Facades\Gate;

class BundleController extends Controller
{
    public function __construct(private BundleService $service){}

    public function index(Store $store)
    {
        Gate::authorize('workWith', $store);

        return BundleResource::collection($store->bundles);
    }
    public function store(StoreBundleRequest $request, Store $store)
    {
        $data = $request->validated();

        $bundle = $this->service->createBundleWithCoupon($store, $data);

        return new BundleResource($bundle);
    }

    public function show(Store $store, Bundle $bundle)
    {
        Gate::authorize('workWith', $store);
        return new BundleResource($bundle);
    }

    public function destroy(Store $store, Bundle $bundle)
    {
        Gate::authorize('workWith', $store);
        $this->service->deleteBundle($bundle);
        return response()->json([
            'message' => ('The bundle was successfully deleted.'),
        ]);
    }

    public function resendAll(Bundle $bundle)
    {
        Gate::authorize('workWith', $bundle->store);
        $result = $this->service->resendAllBundle($bundle);

        return response()->json([
            'message' => 'Poslato :' . $result['sent'] . ' preskoceno : ' . $result['skipped'] . ', ',
            'sent' => $result['sent'],
            'skipped' => $result['skipped'],
        ]);
    }
}
