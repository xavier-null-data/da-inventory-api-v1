<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Movement extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'product_id',
        'source_store_id',
        'target_store_id',
        'quantity',
        'type',
        'timestamp',
    ];

    public $timestamps = false;

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sourceStore()
    {
        return $this->belongsTo(Store::class, 'source_store_id');
    }

    public function targetStore()
    {
        return $this->belongsTo(Store::class, 'target_store_id');
    }
}
