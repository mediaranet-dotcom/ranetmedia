<?php

namespace App\Filament\Widgets;

use App\Models\Odp;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OdpOverviewWidget extends BaseWidget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini

    protected function getStats(): array
    {
        $totalOdps = Odp::count();
        $activeOdps = Odp::where('status', 'active')->count();
        $nearCapacityOdps = Odp::whereRaw('(used_ports / total_ports) >= 0.8')->count();
        $totalPorts = Odp::sum('total_ports');
        $usedPorts = Odp::sum('used_ports');
        $utilizationPercentage = $totalPorts > 0 ? round(($usedPorts / $totalPorts) * 100, 1) : 0;

        return [
            Stat::make('Total ODP', $totalOdps)
                ->description('Optical Distribution Points')
                ->descriptionIcon('heroicon-m-signal')
                ->color('primary'),

            Stat::make('ODP Aktif', $activeOdps)
                ->description('Sedang beroperasi')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Mendekati Kapasitas', $nearCapacityOdps)
                ->description('ODP dengan utilisasi >80%')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($nearCapacityOdps > 0 ? 'warning' : 'success'),

            Stat::make('Utilisasi Port', $utilizationPercentage . '%')
                ->description("{$usedPorts}/{$totalPorts} port digunakan")
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color($utilizationPercentage > 80 ? 'warning' : 'success'),
        ];
    }
}
