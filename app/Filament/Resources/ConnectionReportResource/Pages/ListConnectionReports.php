<?php

namespace App\Filament\Resources\ConnectionReportResource\Pages;

use App\Filament\Resources\ConnectionReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListConnectionReports extends ListRecords
{
    protected static string $resource = ConnectionReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_all')
                ->label('Export All Data')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // This will be handled by the bulk action
                    $this->redirect(static::getResource()::getUrl('index'));
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Connections')
                ->badge(fn () => \App\Models\Service::count()),
            
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active'))
                ->badge(fn () => \App\Models\Service::where('status', 'active')->count())
                ->badgeColor('success'),
            
            'inactive' => Tab::make('Inactive')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'inactive'))
                ->badge(fn () => \App\Models\Service::where('status', 'inactive')->count())
                ->badgeColor('gray'),
            
            'suspended' => Tab::make('Suspended')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'suspended'))
                ->badge(fn () => \App\Models\Service::where('status', 'suspended')->count())
                ->badgeColor('warning'),
            
            'high_utilization' => Tab::make('High Utilization ODPs')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereHas('odp', function (Builder $query) {
                        $query->whereRaw('(used_ports / total_ports) > 0.8');
                    });
                })
                ->badge(function () {
                    return \App\Models\Service::whereHas('odp', function (Builder $query) {
                        $query->whereRaw('(used_ports / total_ports) > 0.8');
                    })->count();
                })
                ->badgeColor('danger'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ConnectionReportResource\Widgets\ConnectionStatsWidget::class,
        ];
    }
}
