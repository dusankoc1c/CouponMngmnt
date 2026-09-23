<?php

namespace App\Repositories\Contracts;

use App\Models\Bundle;

interface BundleRepositoryInterface
{
    public function create(array $data): Bundle;

    public function delete(Bundle $bundle): void;

    public function findById(int $id): ?Bundle;
}
