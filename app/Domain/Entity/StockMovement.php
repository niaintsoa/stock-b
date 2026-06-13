<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\Laravel\Eloquent\Filter\PartialSearchFilter;
use ApiPlatform\Laravel\Eloquent\Filter\OrderFilter;
use ApiPlatform\Laravel\Eloquent\Filter\EqualsFilter;
use ApiPlatform\Laravel\Eloquent\Filter\DateFilter;

#[ApiResource(
    parameters: [
        'reason' => new QueryParameter(filter: PartialSearchFilter::class),
        'type' => new QueryParameter(filter: EqualsFilter::class),
        'status' => new QueryParameter(filter: EqualsFilter::class),
        'product_id' => new QueryParameter(filter: EqualsFilter::class),
        'created_at' => new QueryParameter(filter: DateFilter::class),
        'expiry_date' => new QueryParameter(filter: DateFilter::class),
        'sort[:property]' => new QueryParameter(filter: OrderFilter::class),
    ]
)]
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
