<?php

namespace App\Infrastructure\Filament\Widgets;

use App\Domain\Entity\Customer;
use App\Domain\Entity\Product;
use App\Domain\Entity\StockMovement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $lowStockCount = Product::get()->filter(fn ($p) => $p->current_stock < 10)->count();
        
        $expiringSoon = StockMovement::where('type', 'entry')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->count();

        return [
            Stat::make('Total Produits', Product::count())
                ->description('Nombre total de produits au catalogue')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Stock Faible', $lowStockCount)
                ->description('Produits dont le stock est sous 10 unités')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),

            Stat::make('Lots bientôt expirés', $expiringSoon)
                ->description('Entrées expirant dans moins de 30 jours')
                ->descriptionIcon('heroicon-m-clock')
                ->color($expiringSoon > 0 ? 'warning' : 'success'),
        ];
    }
}
