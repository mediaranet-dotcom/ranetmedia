<?php

namespace App\Filament\Resources\ConnectionReportResource\Widgets;

use App\Models\Service;
use App\Models\Odp;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ConnectionStatsWidget extends BaseWidget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini

    protected function getStats(): array
    {
        // $totalConnections = Service::count();
        // $activeConnections = Service::where('status', 'active')->count();
        // $totalOdps = Odp::count();
        // $activeOdps = Odp::where('status', 'active')->count();
        // $totalCustomers = Customer::count();
        // $activeCustomers = Customer::where('status', 'active')->count();

        // Calculate utilization statistics
        // $totalPorts = Odp::sum('total_ports');
        // $usedPorts = Odp::sum('used_ports');
        // $overallUtilization = $totalPorts > 0 ? round(($usedPorts / $totalPorts) * 100, 1) : 0;

        // Count ODPs by utilization
        // $highUtilizationOdps = Odp::whereRaw('(used_ports / total_ports) > 0.8')->count();
        // $criticalUtilizationOdps = Odp::whereRaw('(used_ports / total_ports) > 0.9')->count();

        // Signal strength statistics
        // $excellentSignal = Service::where('signal_strength', '>=', -20)->count();
        // $goodSignal = Service::whereBetween('signal_strength', [-30, -20])->count();
        // $poorSignal = Service::where('signal_strength', '<', -30)->count();

        return [
            // Stat::make('Total Connections', $totalConnections)
            //     ->description($activeConnections . ' active connections')
            //     ->descriptionIcon('heroicon-m-arrow-trending-up')
            //     ->color('primary'),

            // Stat::make('Total ODPs', $totalOdps)
            //     ->description($activeOdps . ' active ODPs')
            //     ->descriptionIcon('heroicon-m-signal')
            //     ->color('success'),

            // Stat::make('Port Utilization', $overallUtilization . '%')
            //     ->description($usedPorts . ' of ' . $totalPorts . ' ports used')
            //     ->descriptionIcon($overallUtilization > 80 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
            //     ->color($overallUtilization > 80 ? 'danger' : ($overallUtilization > 60 ? 'warning' : 'success')),

            // Stat::make('High Utilization ODPs', $highUtilizationOdps)
            //     ->description($criticalUtilizationOdps . ' critical (>90%)')
            //     ->descriptionIcon('heroicon-m-exclamation-triangle')
            //     ->color($criticalUtilizationOdps > 0 ? 'danger' : ($highUtilizationOdps > 0 ? 'warning' : 'success')),

            // Stat::make('Signal Quality', 'Mixed')
            //     ->description("Excellent: {$excellentSignal}, Good: {$goodSignal}, Poor: {$poorSignal}")
            //     ->descriptionIcon('heroicon-m-signal')
            //     ->color($poorSignal > ($excellentSignal + $goodSignal) ? 'danger' : 'success'),

            // Stat::make('Active Customers', $activeCustomers)
            //     ->description('of ' . $totalCustomers . ' total customers')
            //     ->descriptionIcon('heroicon-m-users')
            //     ->color('info'),
        ];
    }
}
