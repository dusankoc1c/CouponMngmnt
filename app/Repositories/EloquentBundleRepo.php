<?php


namespace App\Repositories;

use App\Models\Bundle;
use App\Repositories\Contracts\BundleRepositoryInterface;

class EloquentBundleRepo implements BundleRepositoryInterface
{
    public function create(array $data): Bundle
    {
        return Bundle::create($data);
    }

    public function delete(Bundle $bundle): void
    {
        $bundle->delete();
    }

    public function findById(int $id): ?Bundle
    {
        return Bundle::find($id);
    }
}
