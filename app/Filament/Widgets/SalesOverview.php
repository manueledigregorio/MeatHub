<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;

class SalesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // Vendite oggi
        $todaySales = Order::whereDate('created_at', $today)->sum('total');
        $todayOrders = Order::whereDate('created_at', $today)->count();

        // Vendite questa settimana
        $weekSales = Order::where('created_at', '>=', $thisWeek)->sum('total');
        $weekOrders = Order::where('created_at', '>=', $thisWeek)->count();

        // Vendite questo mese
        $monthSales = Order::where('created_at', '>=', $thisMonth)->sum('total');
        $monthOrders = Order::where('created_at', '>=', $thisMonth)->count();

        // Prodotti con stock basso
        $lowStockProducts = Product::lowStock()->count();

        // Prodotto più venduto del mese
        $topProduct = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.created_at', '>=', $thisMonth)
            ->selectRaw('products.name, SUM(order_items.quantity) as total_quantity')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->first();

        return [
            Stat::make('Vendite Oggi', '€ ' . number_format($todaySales, 2))
                ->description($todayOrders . ' ordini completati')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),

            Stat::make('Vendite Settimana', '€ ' . number_format($weekSales, 2))
                ->description($weekOrders . ' ordini questa settimana')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Vendite Mese', '€ ' . number_format($monthSales, 2))
                ->description($monthOrders . ' ordini questo mese')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('Stock Basso', $lowStockProducts)
                ->description('Prodotti con stock <= 5')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockProducts > 0 ? 'warning' : 'success'),

            Stat::make('Top Prodotto', $topProduct ? $topProduct->name : 'Nessuno')
                ->description($topProduct ? number_format($topProduct->total_quantity, 2) . ' unità vendute' : 'Nessuna vendita')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
        ];
    }
}