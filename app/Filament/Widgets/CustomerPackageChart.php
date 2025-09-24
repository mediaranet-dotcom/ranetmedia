<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Package;

class CustomerPackageChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Pelanggan per Paket';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        try {
            // Get package distribution through services
            $packages = Package::withCount('services')->where('is_active', true)->get();

            $labels = [];
            $data = [];
            $colors = [
                'rgba(59, 130, 246, 0.8)',   // Blue
                'rgba(34, 197, 94, 0.8)',    // Green
                'rgba(249, 115, 22, 0.8)',   // Orange
                'rgba(168, 85, 247, 0.8)',   // Purple
                'rgba(236, 72, 153, 0.8)',   // Pink
                'rgba(14, 165, 233, 0.8)',   // Sky
                'rgba(132, 204, 22, 0.8)',   // Lime
                'rgba(245, 101, 101, 0.8)',  // Red
            ];

            foreach ($packages as $package) {
                $labels[] = $package->name;
                $data[] = $package->services_count; // Use services count as proxy for customers
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Jumlah Layanan',
                        'data' => $data,
                        'backgroundColor' => array_slice($colors, 0, count($data)),
                        'borderColor' => array_map(function ($color) {
                            return str_replace('0.8', '1', $color);
                        }, array_slice($colors, 0, count($data))),
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => $labels,
            ];
        } catch (\Exception) {
            // Return empty data if there's an error
            return [
                'datasets' => [
                    [
                        'label' => 'Jumlah Layanan',
                        'data' => [0],
                        'backgroundColor' => ['rgba(156, 163, 175, 0.8)'],
                        'borderColor' => ['rgb(156, 163, 175)'],
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => ['Tidak ada data'],
            ];
        }
    }

    protected function getType(): string
    {
        return 'doughnut';
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
