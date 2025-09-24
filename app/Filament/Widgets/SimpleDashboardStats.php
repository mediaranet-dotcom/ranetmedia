<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Invoice;

class SimpleDashboardStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        try {
            $totalCustomers = Customer::count();
            $totalPayments = Payment::where('status', 'completed')->sum('amount');
            $thisMonthPayments = Payment::where('status', 'completed')
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount');
            $outstandingInvoices = Invoice::whereIn('status', ['sent', 'partial_paid', 'overdue'])->count();

            return [
                Stat::make('Total Pelanggan', number_format($totalCustomers))
                    ->description('Pelanggan terdaftar')
                    ->descriptionIcon('heroicon-m-users')
                    ->color('primary'),

                Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($thisMonthPayments, 0, ',', '.'))
                    ->description('Total pembayaran bulan ini')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('success'),

                Stat::make('Total Pendapatan', 'Rp ' . number_format($totalPayments, 0, ',', '.'))
                    ->description('Semua pembayaran selesai')
                    ->descriptionIcon('heroicon-m-currency-dollar')
                    ->color('info'),

                Stat::make('Invoice Tertunda', number_format($outstandingInvoices))
                    ->description('Perlu ditindaklanjuti')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color($outstandingInvoices > 0 ? 'warning' : 'success'),
            ];
        } catch (\Exception) {
            return [
                Stat::make('Error', 'Data tidak tersedia')
                    ->description('Terjadi kesalahan saat memuat data')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger'),
            ];
        }
    }
}
