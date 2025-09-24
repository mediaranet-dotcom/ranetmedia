<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\TicketComment;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Notifications\Notification;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Actions\Action::make('add_comment')
                ->label('Tambah Komentar')
                ->icon('heroicon-o-chat-bubble-left')
                ->color('info')
                ->form([
                    Forms\Components\Textarea::make('content')
                        ->label('Komentar')
                        ->required()
                        ->rows(4),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('is_internal')
                                ->label('Catatan Internal')
                                ->helperText('Hanya visible untuk staff'),
                            Forms\Components\TextInput::make('time_spent_minutes')
                                ->label('Waktu yang Dihabiskan (menit)')
                                ->numeric()
                                ->placeholder('0'),
                        ]),
                ])
                ->action(function (array $data): void {
                    TicketComment::create([
                        'ticket_id' => $this->record->id,
                        'user_id' => auth()->id(),
                        'author_type' => 'staff',
                        'content' => $data['content'],
                        'type' => 'comment',
                        'is_internal' => $data['is_internal'] ?? false,
                        'is_public' => !($data['is_internal'] ?? false),
                        'time_spent_minutes' => $data['time_spent_minutes'] ?? null,
                        'is_billable_time' => false,
                    ]);
                    
                    Notification::make()
                        ->title('Komentar berhasil ditambahkan')
                        ->success()
                        ->send();
                        
                    $this->refreshFormData(['comments']);
                }),
                
            Actions\Action::make('change_status')
                ->label('Ubah Status')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('status')
                        ->label('Status Baru')
                        ->options([
                            'open' => 'Buka',
                            'in_progress' => 'Dalam Proses',
                            'pending_customer' => 'Menunggu Customer',
                            'pending_vendor' => 'Menunggu Vendor',
                            'resolved' => 'Selesai',
                            'closed' => 'Ditutup',
                            'cancelled' => 'Dibatalkan',
                        ])
                        ->required()
                        ->default($this->record->status),
                    Forms\Components\Textarea::make('comment')
                        ->label('Catatan Perubahan')
                        ->placeholder('Alasan perubahan status...')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $oldStatus = $this->record->status;
                    $this->record->update(['status' => $data['status']]);
                    
                    // Add status change comment
                    TicketComment::create([
                        'ticket_id' => $this->record->id,
                        'user_id' => auth()->id(),
                        'author_type' => 'staff',
                        'content' => $data['comment'] ?? "Status diubah dari {$oldStatus} ke {$data['status']}",
                        'type' => 'status_change',
                        'old_value' => $oldStatus,
                        'new_value' => $data['status'],
                        'is_public' => true,
                    ]);
                    
                    Notification::make()
                        ->title('Status berhasil diubah')
                        ->success()
                        ->send();
                        
                    $this->refreshFormData(['status', 'comments']);
                }),
                
            Actions\Action::make('escalate')
                ->label('Eskalasi')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->visible(fn () => !$this->record->is_escalated)
                ->form([
                    Forms\Components\Select::make('escalation_level')
                        ->label('Level Eskalasi')
                        ->options([
                            1 => 'Supervisor',
                            2 => 'Manager',
                            3 => 'Director',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('escalation_reason')
                        ->label('Alasan Eskalasi')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'is_escalated' => true,
                        'escalation_level' => $data['escalation_level'],
                    ]);
                    
                    // Update SLA tracking
                    if ($this->record->slaTracking) {
                        $this->record->slaTracking->markEscalated(
                            $data['escalation_level'],
                            $data['escalation_reason']
                        );
                    }
                    
                    // Add escalation comment
                    TicketComment::create([
                        'ticket_id' => $this->record->id,
                        'user_id' => auth()->id(),
                        'author_type' => 'staff',
                        'content' => "Ticket dieskalasi ke level {$data['escalation_level']}: {$data['escalation_reason']}",
                        'type' => 'escalation',
                        'is_public' => false,
                        'is_internal' => true,
                    ]);
                    
                    Notification::make()
                        ->title('Ticket berhasil dieskalasi')
                        ->success()
                        ->send();
                        
                    $this->refreshFormData(['is_escalated', 'escalation_level', 'comments']);
                }),
        ];
    }
}
