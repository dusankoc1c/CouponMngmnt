<?php



namespace App\Repositories;

use App\Models\Bundle;
use App\Models\User;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentAdminRepo implements AdminRepositoryInterface
{
    public function getAllAdmins(): Collection
    {
        return User::role('admin')->get();
    }
    public function update(User $admin, array $data): User
    {
        $admin->update($data);
        return $admin;
    }
    public function delete(User $admin): void
    {
        $admin->delete();
    }
}
