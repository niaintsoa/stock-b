<?php

namespace App\Infrastructure\Filament\Widgets;

use App\Domain\Entity\Customer;
use App\Domain\Entity\Product;
use App\Domain\Entity\Admin;
use App\Domain\Entity\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{
    // Polling interval for live updates
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        
        // Low stock products are calculated in PHP since current_stock is an accessor
        // For larger apps, this should be a DB column updated via events
        $lowStockCount = Product::all()->filter(fn ($product) => $product->current_stock <= 10)->count();

        $totalUsers = User::count();
        $totalCustomers = Customer::count();
        $totalAdmins = Admin::count();

        return [
            Stat::make('Total Produits', $totalProducts)
                ->description($activeProducts . ' actifs')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Produits en rupture / stock faible', $lowStockCount)
                ->description('Stock inférieur ou égal à 10')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),

            Stat::make('Total Utilisateurs', $totalUsers)
                ->description($totalCustomers . ' Clients, ' . $totalAdmins . ' Admins')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }
}
