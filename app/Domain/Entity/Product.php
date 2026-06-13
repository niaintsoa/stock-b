<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
class Product extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\Domain\Entity\ProductFactory::new();
    }

    protected $fillable = [
        'name',
        'description',
        'price',
        'status',
        'created_by',
        'updated_by',
        'price_change_reason',
    ];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getCurrentStockAttribute()
    {
        $entries = $this->stockMovements()
            ->where('type', 'entry')
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->withSum('children', 'quantity')
            ->get();

        return $entries->sum(function ($entry) {
            return $entry->quantity - ($entry->children_sum_quantity ?? 0);
        });
    }

    /**
     * Tente de retirer du stock selon la méthode FIFO (les lots expirant en premier sont consommés d'abord).
     */
    public function depleteStock(int $quantity, ?string $reason, int $userId): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException("La quantité doit être positive.");
        }

        if ($this->current_stock < $quantity) {
            throw new \Exception("Stock insuffisant pour cette opération.");
        }

        // Récupérer les entrées valides par date d'expiration (les null à la fin, ou les plus proches d'expirer en premier)
        $validEntries = $this->stockMovements()
            ->where('type', 'entry')
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->withSum('children', 'quantity')
            ->orderBy('expiry_date', 'asc') // FIFO sur l'expiration
            ->get();

        $remainingQuantity = $quantity;

        foreach ($validEntries as $entry) {
            $availableInEntry = $entry->quantity - ($entry->children_sum_quantity ?? 0);

            if ($availableInEntry <= 0) {
                continue;
            }

            $quantityToTake = min($availableInEntry, $remainingQuantity);

            $this->stockMovements()->create([
                'type' => 'exit',
                'quantity' => $quantityToTake,
                'reason' => $reason,
                'status' => 'completed',
                'parent_id' => $entry->id,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $remainingQuantity -= $quantityToTake;

            if ($remainingQuantity === 0) {
                break;
            }
        }
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
