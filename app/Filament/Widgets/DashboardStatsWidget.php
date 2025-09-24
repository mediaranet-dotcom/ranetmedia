<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Invoice;

class DashboardStatsWidget extends BaseWidget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini

    protected function getStats(): array
    {
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'active')->count();
        $totalPayments = Payment::where('status', 'completed')->sum('amount');
        $thisMonthPayments = Payment::where('status', 'completed')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
        $outstandingInvoices = Invoice::whereIn('status', ['sent', 'partial_paid', 'overdue'])->count();

        return [
            Stat::make('Total Pelanggan', number_format($totalCustomers))
                ->description($activeCustomers . ' pelanggan aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($thisMonthPayments, 0, ',', '.'))
                ->description('Total pembayaran bulan ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([15, 4, 10, 2, 12, 4, 12]),

            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalPayments, 0, ',', '.'))
                ->description('Semua pembayaran selesai')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info')
                ->chart([3, 8, 15, 6, 10, 9, 7]),

            Stat::make('Invoice Tertunda', number_format($outstandingInvoices))
                ->description('Perlu ditindaklanjuti')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($outstandingInvoices > 0 ? 'warning' : 'success')
                ->chart([2, 10, 5, 22, 8, 4, 12]),
        ];
    }
}
