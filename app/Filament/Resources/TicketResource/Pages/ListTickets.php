<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Ticket Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge(fn () => $this->getModel()::count()),
                
            'open' => Tab::make('Buka')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'open'))
                ->badge(fn () => $this->getModel()::where('status', 'open')->count())
                ->badgeColor('info'),
                
            'in_progress' => Tab::make('Dalam Proses')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'in_progress'))
                ->badge(fn () => $this->getModel()::where('status', 'in_progress')->count())
                ->badgeColor('warning'),
                
            'pending' => Tab::make('Menunggu')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['pending_customer', 'pending_vendor']))
                ->badge(fn () => $this->getModel()::whereIn('status', ['pending_customer', 'pending_vendor'])->count())
                ->badgeColor('secondary'),
                
            'overdue' => Tab::make('Terlambat')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('sla_due_at', '<', now())
                          ->whereNotIn('status', ['resolved', 'closed', 'cancelled'])
                )
                ->badge(fn () => $this->getModel()::where('sla_due_at', '<', now())
                    ->whereNotIn('status', ['resolved', 'closed', 'cancelled'])
                    ->count())
                ->badgeColor('danger'),
                
            'escalated' => Tab::make('Tereskalasi')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_escalated', true))
                ->badge(fn () => $this->getModel()::where('is_escalated', true)->count())
                ->badgeColor('danger'),
                
            'my_tickets' => Tab::make('Ticket Saya')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('assigned_to', auth()->id()))
                ->badge(fn () => $this->getModel()::where('assigned_to', auth()->id())->count())
                ->badgeColor('success'),
                
            'resolved' => Tab::make('Selesai')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'resolved'))
                ->badge(fn () => $this->getModel()::where('status', 'resolved')->count())
                ->badgeColor('success'),
                
            'closed' => Tab::make('Ditutup')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'closed'))
                ->badge(fn () => $this->getModel()::where('status', 'closed')->count())
                ->badgeColor('secondary'),
        ];
    }
}
