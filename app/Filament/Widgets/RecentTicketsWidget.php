<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTicketsWidget extends BaseWidget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static ?string $heading = 'Recent Tickets';

    protected static ?int $sort = 9;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->with(['customer', 'category', 'priority', 'assignedTo'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Ticket #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color(fn($record) => match ($record->category->name) {
                        'Gangguan Internet' => 'danger',
                        'Instalasi Baru' => 'success',
                        'Tagihan & Pembayaran' => 'warning',
                        'Upgrade/Downgrade Paket' => 'info',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('priority.name')
                    ->label('Priority')
                    ->badge()
                    ->color(fn($record) => match ($record->priority->name) {
                        'Rendah' => 'success',
                        'Sedang' => 'warning',
                        'Tinggi' => 'danger',
                        'Kritis' => 'danger',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($record) => match ($record->status) {
                        'open' => 'info',
                        'in_progress' => 'warning',
                        'pending_customer', 'pending_vendor' => 'secondary',
                        'resolved', 'closed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'open' => 'Open',
                        'in_progress' => 'Progress',
                        'pending_customer' => 'Pending',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned')
                    ->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                // Actions removed to avoid route issues
            ])
            ->paginated(false);
    }
}
