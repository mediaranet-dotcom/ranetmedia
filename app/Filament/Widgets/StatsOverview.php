<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Odp;
use App\Models\Payment;
use App\Models\Invoice;

class StatsOverview extends BaseWidget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        // Customer stats
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'active')->count();
        $newCustomersThisMonth = Customer::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Service stats
        $totalServices = Service::count();
        $activeServices = Service::where('status', 'active')->count();
        $suspendedServices = Service::where('status', 'suspended')->count();

        // ODP stats
        $totalOdps = Odp::count();
        $totalPorts = Odp::sum('total_ports');
        $usedPorts = Odp::sum('used_ports');
        $utilizationPercentage = $totalPorts > 0 ? round(($usedPorts / $totalPorts) * 100, 1) : 0;

        // Revenue stats
        $thisMonthRevenue = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $todayRevenue = Payment::whereDate('payment_date', today())->sum('amount');

        // Invoice stats
        $totalInvoices = Invoice::count();
        $paidInvoices = Invoice::where('status', 'paid')->count();
        $unpaidInvoices = Invoice::where('status', '!=', 'paid')->count();

        // Signal quality stats
        $excellentSignal = Service::where('signal_strength', '>=', -20)->count();
        $poorSignal = Service::where('signal_strength', '<', -30)->count();

        return [
            Stat::make('Total Customers', number_format($totalCustomers))
                ->description($activeCustomers . ' active, ' . $newCustomersThisMonth . ' new this month')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 12, 8, 15, 10, 18, $newCustomersThisMonth]),

            Stat::make('Active Services', number_format($activeServices))
                ->description($suspendedServices . ' suspended of ' . $totalServices . ' total')
                ->descriptionIcon($suspendedServices > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($suspendedServices > 0 ? 'warning' : 'success'),

            Stat::make('Port Utilization', $utilizationPercentage . '%')
                ->description($usedPorts . ' of ' . $totalPorts . ' ports used')
                ->descriptionIcon($utilizationPercentage > 80 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-signal')
                ->color($utilizationPercentage > 80 ? 'danger' : ($utilizationPercentage > 60 ? 'warning' : 'success'))
                ->chart([45, 52, 48, 61, 58, 67, $utilizationPercentage]),

            Stat::make('Monthly Revenue', 'Rp ' . number_format($thisMonthRevenue, 0, ',', '.'))
                ->description('Today: Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Invoice Status', $paidInvoices . ' Paid')
                ->description($unpaidInvoices . ' unpaid of ' . $totalInvoices . ' total')
                ->descriptionIcon($unpaidInvoices > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($unpaidInvoices > 0 ? 'warning' : 'success'),

            Stat::make('Signal Quality', $excellentSignal . ' Excellent')
                ->description($poorSignal . ' poor signals need attention')
                ->descriptionIcon($poorSignal > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-signal')
                ->color($poorSignal > 0 ? 'warning' : 'success'),

            Stat::make('Active ODPs', number_format($totalOdps))
                ->description('Network distribution points')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('info'),
        ];
    }
}
