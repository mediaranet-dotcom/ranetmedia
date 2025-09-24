<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerStatsWidget extends BaseWidget
{
    protected static bool $isDiscovered = false; // Widget ini dinonaktifkan
    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 1; // Widget utama pertama

    protected function getStats(): array
    {
        // Total customers
        $totalCustomers = Customer::count();

        // Customers with payments this month
        $paidThisMonth = Customer::whereHas('payments', function ($query) {
            $query->whereMonth('payments.created_at', now()->month)
                ->whereYear('payments.created_at', now()->year);
        })->count();

        // Total payment amount this month
        $totalPaymentThisMonth = Payment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Unpaid customers this month
        $unpaidThisMonth = $totalCustomers - $paidThisMonth;

        return [
            Stat::make('Total Pelanggan', $totalCustomers)
                ->description('Total semua pelanggan')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Sudah Bayar Bulan Ini', $paidThisMonth)
                ->description('Pelanggan yang sudah membayar')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Belum Bayar Bulan Ini', $unpaidThisMonth)
                ->description('Pelanggan yang belum membayar')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Total Pembayaran', 'Rp ' . number_format($totalPaymentThisMonth, 0, ',', '.'))
                ->description('Total pembayaran bulan ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
