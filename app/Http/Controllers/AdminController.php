<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
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
    public function __construct(private AdminService $adminService){}
    public function index(){
        $admins = User::where('role', 'admin')->get();

        return view('admins.index', ['admins' => $admins]);
    }

    public function destroy(User $user){

        $this->adminService->deleteAdmin($user);

        return redirect('/superadmin/admins')->with('success', 'Admin has been deleted');

    }
    public function exportAll(ExportAllCodesRequest $request)
    {
        $data = $request->validated();

        $this->adminService->exportCsv($data['store_ids'], $request);
    }

    public function update(UpdateAdminRequest $request, User $admin)
    {
        $this->adminService->updateAdmin($admin, $request);
        return redirect('/superadmin/admins')->with('success', 'Admin has been updated');
    }

}
