<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
        'category',
        'price',
        'sku',
    ];

    public function inventories()
    {
        // Un producto puede estar disponible en varias tiendas
        return $this->hasMany(Inventory::class);
    }

    public function movements()
    {
        // Un producto puede tener muchos movimientos asociados
        return $this->hasMany(Movement::class);
    }
}
