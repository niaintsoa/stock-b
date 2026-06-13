<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ApiResource]
class StockMovement extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\Domain\Entity\StockMovementFactory::new();
    }
    protected $fillable = [
        'product_id',
        'quantity',
        'type', // 'entry' ou 'exit'
        'reason',
        'status',
        'created_by',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
