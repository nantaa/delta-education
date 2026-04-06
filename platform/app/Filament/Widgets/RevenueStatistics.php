<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Participant;
use App\Models\Webinar;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueStatistics extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Order::where('status', 'paid')->sum('total_amount');
        $pendingRevenue = Order::where('status', 'pending')->sum('total_amount');
        $totalParticipants = Participant::count();
        $totalWebinars = Webinar::count();
        $publishedWebinars = Webinar::where('status', 'published')->count();

        // Month-over-month comparison
        $thisMonth = Order::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $lastMonth = Order::where('status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_amount');

        $monthGrowth = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : ($thisMonth > 0 ? 100 : 0);

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Semua transaksi paid')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($thisMonth, 0, ',', '.'))
                ->description($monthGrowth >= 0 ? "+{$monthGrowth}% dari bulan lalu" : "{$monthGrowth}% dari bulan lalu")
                ->descriptionIcon($monthGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('Menunggu Pembayaran', 'Rp ' . number_format($pendingRevenue, 0, ',', '.'))
                ->description('Transaksi pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Total Peserta', number_format($totalParticipants, 0, ',', '.'))
                ->description('Terdaftar di semua event')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Webinar Aktif', $publishedWebinars . ' / ' . $totalWebinars)
                ->description('Published / Total')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
        ];
    }
}
