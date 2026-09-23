<?php

namespace App\Repositories\Contracts;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Collection;

interface AdminRepositoryInterface
{
    public function getAllAdmins(): Collection;

    public function update(User $admin, array $data): User;

    public function delete(User $admin): void;

}
