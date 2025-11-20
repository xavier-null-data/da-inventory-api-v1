<?php

namespace App\Repositories;

use App\Models\Movement;

class MovementRepository
{
    public function create(array $data): Movement
    {
        return Movement::create($data);
    }
}
