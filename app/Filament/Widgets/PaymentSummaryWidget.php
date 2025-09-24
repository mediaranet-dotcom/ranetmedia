<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentSummaryWidget extends BaseWidget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 1; // Widget utama pertama

    protected function getStats(): array
    {
        $thisMonth = now()->format('Y-m');
        $lastMonth = now()->subMonth()->format('Y-m');

        // Total customers
        $totalCustomers = Customer::count();

        // Customers who paid this month
        $paidThisMonth = Customer::whereHas('payments', function ($query) use ($thisMonth) {
            $query->whereRaw("DATE_FORMAT(payment_date, '%Y-%m') = ?", [$thisMonth]);
        })->count();

        // Total payment this month
        $totalPaymentThisMonth = Payment::whereRaw("DATE_FORMAT(payment_date, '%Y-%m') = ?", [$thisMonth])
            ->sum('amount') ?? 0;

        // Total payment last month
        $totalPaymentLastMonth = Payment::whereRaw("DATE_FORMAT(payment_date, '%Y-%m') = ?", [$lastMonth])
            ->sum('amount') ?? 0;

        // Calculate percentage change
        $paymentChange = 0;
        if ($totalPaymentLastMonth > 0) {
            $paymentChange = (($totalPaymentThisMonth - $totalPaymentLastMonth) / $totalPaymentLastMonth) * 100;
        }

        // Customers who haven't paid this month
        $unpaidThisMonth = $totalCustomers - $paidThisMonth;

        return [
            Stat::make('Total Pelanggan', $totalCustomers)
                ->description('Total pelanggan terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Sudah Bayar Bulan Ini', $paidThisMonth)
                ->description($unpaidThisMonth . ' belum bayar')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Pembayaran Bulan Ini', 'Rp ' . number_format($totalPaymentThisMonth, 0, ',', '.'))
                ->description(
                    $paymentChange >= 0
                        ? '+' . number_format($paymentChange, 1) . '% dari bulan lalu'
                        : number_format($paymentChange, 1) . '% dari bulan lalu'
                )
                ->descriptionIcon($paymentChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($paymentChange >= 0 ? 'success' : 'danger'),

            Stat::make(
                'Rata-rata per Pelanggan',
                $paidThisMonth > 0
                    ? 'Rp ' . number_format($totalPaymentThisMonth / $paidThisMonth, 0, ',', '.')
                    : 'Rp 0'
            )
                ->description('Pembayaran rata-rata bulan ini')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),
        ];
    }
}
