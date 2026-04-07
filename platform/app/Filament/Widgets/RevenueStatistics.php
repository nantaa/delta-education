<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Participant;
use App\Models\Training;
use App\Models\Webinar;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueStatistics extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // ── Total paid revenue ────────────────────────────────────────
        $totalRevenue  = Order::where('status', 'paid')->sum('total_amount');
        $pendingRevenue = Order::where('status', 'pending')->sum('total_amount');

        // ── Month-over-month comparison ───────────────────────────────
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

        // ── Pelatihan K3 income (via participants amount_paid) ────────
        $trainingRevenue = Participant::where('participatable_type', Training::class)
            ->where('payment_status', 'settlement')
            ->sum('amount_paid');

        $trainingThisMonth = Participant::where('participatable_type', Training::class)
            ->where('payment_status', 'settlement')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount_paid');

        $trainingPending = Participant::where('participatable_type', Training::class)
            ->where('payment_status', 'pending')
            ->count();

        // ── Participant counts ────────────────────────────────────────
        $totalParticipants    = Participant::count();
        $webinarParticipants  = Participant::where('participatable_type', Webinar::class)->count();
        $trainingParticipants = Participant::where('participatable_type', Training::class)->count();

        // ── Event counts ──────────────────────────────────────────────
        $totalWebinars     = Webinar::count();
        $publishedWebinars = Webinar::where('status', 'published')->count();
        $totalTrainings    = Training::count();
        $activeTrainings   = Training::where('status', 'published')
            ->where('scheduled_at', '>=', now())
            ->count();

        return [
            // ── Overall revenue ───────────────────────────────────────
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue + $trainingRevenue, 0, ',', '.'))
                ->description('Semua transaksi terkonfirmasi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($thisMonth + $trainingThisMonth, 0, ',', '.'))
                ->description($monthGrowth >= 0 ? "+{$monthGrowth}% dari bulan lalu" : "{$monthGrowth}% dari bulan lalu")
                ->descriptionIcon($monthGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('Menunggu Pembayaran', 'Rp ' . number_format($pendingRevenue, 0, ',', '.'))
                ->description("{$trainingPending} peserta K3 belum bayar")
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            // ── Pelatihan K3 breakdown ────────────────────────────────
            Stat::make('Pendapatan Pelatihan K3', 'Rp ' . number_format($trainingRevenue, 0, ',', '.'))
                ->description("{$trainingParticipants} peserta terdaftar · {$activeTrainings}/{$totalTrainings} jadwal aktif")
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),

            // ── Participants ──────────────────────────────────────────
            Stat::make('Total Peserta', number_format($totalParticipants, 0, ',', '.'))
                ->description("Webinar: {$webinarParticipants} · K3: {$trainingParticipants}")
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            // ── Webinars ──────────────────────────────────────────────
            Stat::make('Webinar Aktif', $publishedWebinars . ' / ' . $totalWebinars)
                ->description('Published / Total')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
        ];
    }
}
