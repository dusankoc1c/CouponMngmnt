<?php
namespace App\Services;

use App\Helpers\CsvExportHelper;
use App\Helpers\EmailTemplateHelper;
use App\Models\Coupon;
use App\Models\Store;
use App\Models\User;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\StoreRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
            'value_limit' => $user->default_store_value_limit
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

    public function assertCanAddValue(Store $store, float $additionalAmount): void
    {
        if($store->value_limit == null){
            return;
        }

        $currentTotalValue = $this->calculateTotalValue($store);
        $newTotalValue = $currentTotalValue + $additionalAmount;

        if($newTotalValue > $store->value_limit){
            throw ValidationException::withMessages([
                'discount_amount' => 'Vrednost je premasila VALUE LIMIT zadatat od Super Admina'
            ]);
        }
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

    public function updateEmailTemplate(Store $store, array $data): Store
    {
        return $this->storeRepository->update($store, [
            'initial_email_template' => $data['initial_email_template'],
            'reminder_email_template' => $data['reminder_email_template'],
        ]);
    }

    public function getInitialEmailTemplate(Store $store): string
    {
        if ($store->initial_email_template != null) {
            return $store->initial_email_template;
        }

        return EmailTemplateHelper::getDefaultInitialTemplate();
    }

    public function getReminderEmailTemplate(Store $store): string
    {
        if ($store->reminder_email_template != null) {
            return $store->reminder_email_template;
        }

        return EmailTemplateHelper::getDefaultReminderTemplate();
    }
}
