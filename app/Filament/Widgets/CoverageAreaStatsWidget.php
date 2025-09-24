<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CoverageAreaStatsWidget extends BaseWidget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static ?string $pollingInterval = '30s';

    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        try {
            // Total customers
            $totalCustomers = Customer::count();

            // Total districts covered
            $totalDistricts = Customer::whereNotNull('district')
                ->distinct()
                ->count('district');

            // Total villages covered
            $totalVillages = Customer::whereNotNull('village')
                ->distinct()
                ->count('village');

            // Active services
            $activeServices = Customer::whereHas('services', function ($query) {
                $query->where('status', 'active');
            })->count();

            return [
                Stat::make('Total Customers', $totalCustomers)
                    ->description('Total pelanggan terdaftar')
                    ->descriptionIcon('heroicon-m-users')
                    ->color('primary'),

                Stat::make('Kecamatan Tercakup', $totalDistricts)
                    ->description('Total kecamatan yang dilayani')
                    ->descriptionIcon('heroicon-m-map')
                    ->color('success'),

                Stat::make('Desa/Kelurahan', $totalVillages)
                    ->description('Total desa yang dilayani')
                    ->descriptionIcon('heroicon-m-home-modern')
                    ->color('info'),

                Stat::make('Layanan Aktif', $activeServices)
                    ->description('Pelanggan dengan layanan aktif')
                    ->descriptionIcon('heroicon-m-signal')
                    ->color('warning'),
            ];
        } catch (\Exception $e) {
            return [
                Stat::make('Error', 0)
                    ->description('Gagal memuat data')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger'),
            ];
        }
    }
}
