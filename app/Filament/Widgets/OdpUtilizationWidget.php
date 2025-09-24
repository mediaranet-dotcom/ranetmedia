<?php

namespace App\Filament\Widgets;

use App\Models\Odp;
use Filament\Widgets\Widget;

class OdpUtilizationWidget extends Widget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static string $view = 'filament.widgets.odp-utilization';

    protected static ?string $heading = 'ODP Port Utilization';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    protected function getViewData(): array
    {
        // Get ODPs with highest utilization
        $topUtilizedOdps = Odp::with(['services.customer'])
            ->get()
            ->map(function ($odp) {
                $utilization = $odp->getUtilizationPercentage();
                return [
                    'id' => $odp->id,
                    'name' => $odp->name,
                    'code' => $odp->code,
                    'used_ports' => $odp->used_ports,
                    'total_ports' => $odp->total_ports,
                    'utilization' => $utilization,
                    'status' => $odp->status,
                    'area' => $odp->area,
                    'customer_count' => $odp->services->count(),
                ];
            })
            ->sortByDesc('utilization')
            ->take(10);

        // Get utilization statistics
        $allOdps = Odp::all();
        $totalOdps = $allOdps->count();
        $activeOdps = $allOdps->where('status', 'active')->count();
        $totalPorts = $allOdps->sum('total_ports');
        $usedPorts = $allOdps->sum('used_ports');
        $overallUtilization = $totalPorts > 0 ? round(($usedPorts / $totalPorts) * 100, 1) : 0;

        // Count ODPs by utilization ranges
        $utilizationRanges = [
            'critical' => 0, // > 90%
            'high' => 0,     // 70-90%
            'medium' => 0,   // 40-70%
            'low' => 0,      // < 40%
        ];

        foreach ($allOdps as $odp) {
            $utilization = $odp->getUtilizationPercentage();
            if ($utilization > 90) {
                $utilizationRanges['critical']++;
            } elseif ($utilization > 70) {
                $utilizationRanges['high']++;
            } elseif ($utilization > 40) {
                $utilizationRanges['medium']++;
            } else {
                $utilizationRanges['low']++;
            }
        }

        return [
            'topUtilizedOdps' => $topUtilizedOdps,
            'stats' => [
                'totalOdps' => $totalOdps,
                'activeOdps' => $activeOdps,
                'totalPorts' => $totalPorts,
                'usedPorts' => $usedPorts,
                'overallUtilization' => $overallUtilization,
            ],
            'utilizationRanges' => $utilizationRanges,
        ];
    }
}
