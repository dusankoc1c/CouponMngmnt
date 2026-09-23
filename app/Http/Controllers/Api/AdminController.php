<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExportAllCodesRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Resources\AdminResource;
use App\Models\User;
use App\Services\AdminService;

class AdminController extends Controller
{
    public function __construct(private AdminService $adminService){}
    public function index()
    {
        $admins = $this->adminService->getAllAdmins();

        return AdminResource::collection($admins);
    }
    public function destroy(User $admin)
    {
        $this->adminService->deleteAdmin($admin);

        return response()->json([
            'message' => 'Admin deleted successfully'
        ]);
    }
    public function update(UpdateAdminRequest $request, User $admin)
    {
        $data = $request->validated();
        $admin = $this->adminService->updateAdmin($admin, $data);
        return new AdminResource($admin);
    }

    public function exportAllCodes(ExportAllCodesRequest $request)
    {
        $data = $request->validated();
        return $this->adminService->exportCsv($data['store_ids'], $request);
    }

}
