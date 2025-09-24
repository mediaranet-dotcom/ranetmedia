<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonthlyPaymentSummaryWidget extends BaseWidget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static ?string $pollingInterval = '30s';

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Bulan ini
        $currentMonth = now();
        $thisMonthPayments = Payment::whereMonth('payment_date', $currentMonth->month)
            ->whereYear('payment_date', $currentMonth->year)
            ->sum('amount');
        $thisMonthCount = Payment::whereMonth('payment_date', $currentMonth->month)
            ->whereYear('payment_date', $currentMonth->year)
            ->count();

        // Bulan lalu
        $lastMonth = now()->subMonth();
        $lastMonthPayments = Payment::whereMonth('payment_date', $lastMonth->month)
            ->whereYear('payment_date', $lastMonth->year)
            ->sum('amount');
        $lastMonthCount = Payment::whereMonth('payment_date', $lastMonth->month)
            ->whereYear('payment_date', $lastMonth->year)
            ->count();

        // Hari ini
        $todayPayments = Payment::whereDate('payment_date', today())->sum('amount');
        $todayCount = Payment::whereDate('payment_date', today())->count();

        // Perhitungan persentase perubahan
        $changePercent = $lastMonthPayments > 0
            ? (($thisMonthPayments - $lastMonthPayments) / $lastMonthPayments) * 100
            : 0;

        // Nama bulan dalam bahasa Indonesia
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $currentMonthName = $months[$currentMonth->month];
        $lastMonthName = $months[$lastMonth->month];

        return [
            Stat::make("Pembayaran {$currentMonthName} {$currentMonth->year}", 'Rp ' . number_format($thisMonthPayments, 0, ',', '.'))
                ->description("{$thisMonthCount} transaksi bulan ini")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([
                    $lastMonthPayments / 1000000, // Konversi ke jutaan untuk chart
                    $thisMonthPayments / 1000000
                ]),

            Stat::make("Pembayaran {$lastMonthName} {$lastMonth->year}", 'Rp ' . number_format($lastMonthPayments, 0, ',', '.'))
                ->description("{$lastMonthCount} transaksi bulan lalu")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Pembayaran Hari Ini', 'Rp ' . number_format($todayPayments, 0, ',', '.'))
                ->description("{$todayCount} transaksi hari ini")
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(
                'Perubahan Bulanan',
                ($changePercent >= 0 ? '+' : '') . number_format($changePercent, 1) . '%'
            )
                ->description($changePercent >= 0 ? 'Naik dari bulan lalu' : 'Turun dari bulan lalu')
                ->descriptionIcon($changePercent >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($changePercent >= 0 ? 'success' : 'danger')
                ->chart([
                    $lastMonthPayments / 1000000,
                    $thisMonthPayments / 1000000
                ]),
        ];
    }
}
