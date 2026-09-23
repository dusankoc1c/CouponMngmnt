<?php


namespace App\Services;

use App\Helpers\CsvExportHelper;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Bundle;
use App\Models\Coupon;
use App\Models\Store;
use App\Models\User;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminService
{
    public function __construct(private AdminRepositoryInterface $adminRepository)
    {

    }

    public function getAllAdmins(): Collection
    {
        return $this->adminRepository->getAllAdmins();
    }

    public function updateAdmin(User $admin, array $data): User
    {
        if ($admin->hasRole('superadmin')) {
            abort(403, 'Nije moguce menjati superadmin nalog');
        }

        return $this->adminRepository->update($admin, [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    public function deleteAdmin(User $admin): void
    {
        if ($admin->hasRole('superadmin')) {
            abort(403, 'Nije moguce menjati superadmin nalog');
        }

        $this->adminRepository->delete($admin);
    }

    public function exportCsv(array $storeIds, Request $request): StreamedResponse
    {
        $bundleIds = Bundle::whereIn('store_id', $storeIds)->pluck('id');

        $query = Coupon::whereIn('bundle_id', $bundleIds)->with('bundle.store');

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request['created_from']);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request['created_to']);
        }
        if ($request->filled('status') && $request['status'] !== 'all') {
            if ($request->status === 'used') {
                $query->where('is_used', true);
            } else {
                $query->where('is_used', false);
            }
        }
        if ($request->filled('amount_min')) {
            $query->where('discount_amount', '>=', $request['amount_min']);
        }
        if ($request->filled('amount_max')) {
            $query->where('discount_amount', '<=', $request['amount_max']);
        }

        $coupons = $query->get();

        return CsvExportHelper::buildCsvExport($coupons, true);
    }
}
