<?php

namespace App\Http\Controllers;

use App\Helpers\CsvExportHelper;
use App\Http\Requests\ExportAllCodesRequest;
use App\Http\Requests\ExportCodesRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Bundle;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        $admins = User::where('role', 'admin')->get();

        return view('admins.index', ['admins' => $admins]);
    }

    public function destroy(User $user){
        if($user->role === 'superadmin'){
            abort(403, 'Nije moguce obrisati superadmin nalog');
        }

        $user->delete();

        return redirect('/superadmin/admins')->with('success', 'Admin has been deleted');
    }
    public function exportAll(ExportAllCodesRequest $request)
    {
        $data = $request->validated();

        $bundleIds = Bundle::whereIn('store_id', $data['store_ids'])->pluck('id');

        $query = Coupon::whereIn('bundle_id', $bundleIds)->with('bundle.store');

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $data['created_from']);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $data['created_to']);
        }
        if ($request->filled('status') && $data['status'] !== 'all') {
            if ($request->status === 'used') {
                $query->where('is_used', true);
            } else {
                $query->where('is_used', false);
            }
        }
        if ($request->filled('amount_min')) {
            $query->where('discount_amount', '>=', $data['amount_min']);
        }
        if ($request->filled('amount_max')) {
            $query->where('discount_amount', '<=', $data['amount_max']);
        }

        $coupons = $query->get();

        return CsvExportHelper::buildCsvExport($coupons, true);
    }

    public function update(UpdateAdminRequest $request, User $admin)
    {
        if ($admin->role === 'superadmin') {
            abort(403, 'Nije moguce izmeniti superadmin nalog');
        }

        $data = $request->validated();

        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->save();

        return redirect('/superadmin/admins')->with('success', 'Admin has been updated');

    }

}
