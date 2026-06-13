<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\Laravel\Eloquent\Filter\PartialSearchFilter;
use ApiPlatform\Laravel\Eloquent\Filter\OrderFilter;
use ApiPlatform\Laravel\Eloquent\Filter\EqualsFilter;

#[ApiResource(
    parameters: [
        'first_name' => new QueryParameter(filter: PartialSearchFilter::class),
        'last_name' => new QueryParameter(filter: PartialSearchFilter::class),
        'phone' => new QueryParameter(filter: PartialSearchFilter::class),
        'status' => new QueryParameter(filter: EqualsFilter::class),
        'sort[:property]' => new QueryParameter(filter: OrderFilter::class),
    ]
)]
class Customer extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\Domain\Entity\CustomerFactory::new();
    }

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'address',
        'status',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
