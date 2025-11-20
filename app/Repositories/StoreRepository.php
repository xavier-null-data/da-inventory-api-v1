<?php

namespace App\Repositories;

use App\Models\Store;

class StoreRepository
{
    public function find(string $id): ?Store
    {
        return Store::find($id);
    }
}
