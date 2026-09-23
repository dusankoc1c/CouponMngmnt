<?php

namespace App\Repositories\Contracts;

use App\Models\Coupon;
use App\Models\Store;

interface StoreRepositoryInterface
{
    public function create(array $data): Store;

    public function update(Store $store, array $data): Store;

    public function delete(Store $store): void;

    public function findById(int $id): ?Store;
}
