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
        return [
            Stat::make('Total Clients', Customer::count())
                ->description('Nombre total de clients enregistrés')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Total Produits', Product::count())
                ->description('Nombre total de produits au catalogue')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),

            Stat::make('Mouvements de stock', StockMovement::count())
                ->description('Toutes les entrées/sorties effectuées')
                ->descriptionIcon('heroicon-m-arrows-up-down')
                ->color('warning'),
        ];
    }
}
