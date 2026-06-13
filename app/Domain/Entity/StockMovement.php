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
        'type',
        'reason',
        'status',
        'created_by',
        'updated_by',
        'expiry_date',
        'parent_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function parent()
    {
        return $this->belongsTo(StockMovement::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(StockMovement::class, 'parent_id');
    }
}
