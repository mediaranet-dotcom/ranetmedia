<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Customer;
use App\Models\Payment;

class PaymentReportWidget extends ChartWidget
{
    protected static ?string $heading = 'Laporan Pembayaran Pelanggan';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Get payment data for the last 6 months
        $months = [];
        $paidCustomers = [];
        $unpaidCustomers = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            // Customers who paid in this month
            $paid = Customer::whereHas('payments', function ($query) use ($date) {
                $query->whereYear('payments.created_at', $date->year)
                    ->whereMonth('payments.created_at', $date->month);
            })->count();

            // Total customers registered up to this month
            $totalUpToMonth = Customer::whereYear('created_at', '<=', $date->year)
                ->where(function ($query) use ($date) {
                    $query->whereYear('created_at', '<', $date->year)
                        ->orWhere(function ($q) use ($date) {
                            $q->whereYear('created_at', $date->year)
                                ->whereMonth('created_at', '<=', $date->month);
                        });
                })->count();

            $unpaid = $totalUpToMonth - $paid;

            $paidCustomers[] = $paid;
            $unpaidCustomers[] = max(0, $unpaid);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sudah Bayar',
                    'data' => $paidCustomers,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Belum Bayar',
                    'data' => $unpaidCustomers,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }
}
