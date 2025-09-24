<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status Pembayaran Bulan Ini';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $currentMonth = Carbon::now();
        
        // Get payment status for current month
        $completed = Payment::where('status', 'completed')
            ->whereMonth('payment_date', $currentMonth->month)
            ->whereYear('payment_date', $currentMonth->year)
            ->count();
            
        $pending = Payment::where('status', 'pending')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
            
        $failed = Payment::where('status', 'failed')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pembayaran',
                    'data' => [$completed, $pending, $failed],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',   // Green for completed
                        'rgba(249, 115, 22, 0.8)',  // Orange for pending
                        'rgba(239, 68, 68, 0.8)',   // Red for failed
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(249, 115, 22)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Selesai', 'Pending', 'Gagal'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
