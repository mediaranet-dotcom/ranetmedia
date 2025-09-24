<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Payment;
use Carbon\Carbon;

class RevenueChart extends ChartWidget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static ?string $heading = 'Grafik Pendapatan Bulanan';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Get revenue data for the last 12 months
        $months = [];
        $revenues = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $revenue = Payment::where('status', 'completed')
                ->whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');

            $revenues[] = $revenue / 1000000; // Convert to millions
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Juta IDR)',
                    'data' => $revenues,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
