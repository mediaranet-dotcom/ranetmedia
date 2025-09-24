<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Models\TicketCategory;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class TicketCategoryChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static ?string $heading = 'Tickets by Category';

    protected static ?int $sort = 8;

    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $categories = TicketCategory::withCount(['tickets' => function ($query) {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        }])->get();

        return [
            'datasets' => [
                [
                    'label' => 'Tickets',
                    'data' => $categories->pluck('tickets_count')->toArray(),
                    'backgroundColor' => $categories->pluck('color')->toArray(),
                    'borderColor' => $categories->pluck('color')->toArray(),
                ],
            ],
            'labels' => $categories->pluck('name')->toArray(),
        ];
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
