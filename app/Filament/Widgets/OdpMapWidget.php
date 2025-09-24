<?php

namespace App\Filament\Widgets;

use App\Models\Odp;
use Filament\Widgets\Widget;

class OdpMapWidget extends Widget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static string $view = 'filament.widgets.odp-leaflet-map';

    protected static ?string $heading = 'Peta Lokasi ODP';

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $odps = Odp::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['services.customer', 'services.package'])
            ->get();

        $locations = [];

        foreach ($odps as $odp) {
            $statusColor = match ($odp->status) {
                'active' => '#10B981',      // Green
                'inactive' => '#6B7280',    // Gray
                'maintenance' => '#F59E0B', // Yellow
                'damaged' => '#EF4444',     // Red
                default => '#6B7280'
            };

            $utilizationPercentage = $odp->getUtilizationPercentage();

            // Get customers connected to this ODP
            $customers = $odp->services->map(function ($service) {
                return [
                    'id' => $service->customer->id,
                    'customer_number' => $service->customer->customer_number,
                    'name' => $service->customer->name,
                    'phone' => $service->customer->phone,
                    'package' => $service->package->name,
                    'port' => $service->odp_port,
                    'status' => $service->status,
                    'signal_strength' => $service->signal_strength,
                    'fiber_color' => $service->fiber_cable_color,
                ];
            })->toArray();

            $locations[] = [
                'lat' => (float) $odp->latitude,
                'lng' => (float) $odp->longitude,
                'name' => $odp->name,
                'code' => $odp->code,
                'id' => $odp->id,
                'status' => $odp->status,
                'statusColor' => $statusColor,
                'address' => $odp->address,
                'area' => $odp->area,
                'district' => $odp->district,
                'condition' => ucfirst($odp->condition),
                'odp_type' => str_replace('_', ' ', ucfirst($odp->odp_type)),
                'port_usage' => "{$odp->used_ports}/{$odp->total_ports}",
                'utilization' => $utilizationPercentage,
                'manufacturer' => $odp->manufacturer,
                'installation_date' => $odp->installation_date?->format('d M Y'),
                'last_maintenance' => $odp->last_maintenance?->format('d M Y') ?? 'Never',
                'used_ports' => $odp->used_ports,
                'total_ports' => $odp->total_ports,
                'customers' => $customers,
            ];
        }

        return [
            'locations' => $locations,
            'center' => [
                'lat' => -6.200000,
                'lng' => 106.816666
            ],
            'zoom' => 12
        ];
    }
}
