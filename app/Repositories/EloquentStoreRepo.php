<?php

namespace App\Repositories;

use App\Models\Store;
use App\Repositories\Contracts\StoreRepositoryInterface;

class EloquentStoreRepo implements StoreRepositoryInterface
{
    public function create(array $data): Store
    {
        return Store::create($data);
    }

    public function update(Store $store, array $data): Store
    {
        $store->update($data);

        return $store;
    }

    public function delete(Store $store): void
    {
        $store->delete();
    }

    public function findById(int $id): ?Store
    {
        return Store::find($id);
    }
}

