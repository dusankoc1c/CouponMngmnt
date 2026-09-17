<?php
namespace App\Services;

use App\Helpers\CsvExportHelper;
use App\Models\Coupon;
use App\Models\Store;
use App\Models\User;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\StoreRepositoryInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StoreService
{

    public function __construct(private StoreRepositoryInterface $storeRepository)
    {}

    public function createStore(User $user, array $data): Store
    {
        return $this->storeRepository->create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'description' => $data['description'],
        ]);
    }

    public function updateStore(Store $store, array $data): Store
    {
        return $this->storeRepository->update($store, [
            'name' => $data['name'],
            'description' => $data['description'],
        ]);
    }

    public function calculateTotalValue(Store $store): float
    {
        $total = 0;

        foreach ($store->bundles as $bundle) {
            $total += $bundle->getTotalValue();
        }

        return $total;
    }

    public function exportCodesToCsv(Store $store, array $bundleIds, Request $request): StreamedResponse
    {
        $query = Coupon::whereIn('bundle_id', $bundleIds)->with('bundle');

        // ------------ FILTERI -------------
        if($request->filled('created_from')){
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if($request->filled('created_to')){
            $query->whereDate('created_at', '<=', $request->created_to);
        }
        if($request->filled('status') && $request->status != 'all'){
            if($request->status == 'used'){
                $query->where('is_used', true);
            }else{
                $query->where('is_used', false);
            }
        }
        if($request->filled('amount_min')){
            $query->where('discount_amount', '>=', $request->amount_min);
        }
        if($request->filled('amount_max')){
            $query->where('discount_amount', '<=', $request->amount_max);
        }

        $coupons = $query->get();

        return CsvExportHelper::buildCsvExport($coupons, false);
    }
}
