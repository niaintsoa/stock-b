<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'status',
        'created_by',
        'updated_by',
    ];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getCurrentStockAttribute()
    {
        $entries = $this->stockMovements()->where('type', 'entry')->sum('quantity');
        $exits = $this->stockMovements()->where('type', 'exit')->sum('quantity');

        return $entries - $exits;
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
