<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\TicketSlaTracking;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $ticket = $this->record;
        
        // Create SLA tracking record
        TicketSlaTracking::create([
            'ticket_id' => $ticket->id,
            'sla_start_time' => $ticket->created_at,
            'sla_due_time' => $ticket->sla_due_at,
            'business_hours_config' => [
                'start_hour' => 8,
                'end_hour' => 17,
                'working_days' => [1, 2, 3, 4, 5], // Monday to Friday
            ],
        ]);

        // TODO: Send notifications to assigned staff and customer
        // TODO: Auto-assign based on category rules
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
