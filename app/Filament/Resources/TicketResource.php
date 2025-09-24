<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Tickets';

    protected static ?string $modelLabel = 'Ticket';

    protected static ?string $pluralModelLabel = 'Tickets';

    protected static ?string $navigationGroup = 'Support';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Ticket')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('ticket_number')
                                    ->label('Nomor Ticket')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('Auto-generated'),
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'open' => 'Buka',
                                        'in_progress' => 'Dalam Proses',
                                        'pending_customer' => 'Menunggu Customer',
                                        'pending_vendor' => 'Menunggu Vendor',
                                        'resolved' => 'Selesai',
                                        'closed' => 'Ditutup',
                                        'cancelled' => 'Dibatalkan',
                                    ])
                                    ->default('open')
                                    ->required(),
                            ]),
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->rows(4),
                    ]),

                Forms\Components\Section::make('Customer & Service')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('customer_id')
                                    ->label('Customer')
                                    ->relationship('customer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $customer = Customer::find($state);
                                            if ($customer) {
                                                $set('contact_value', $customer->email);
                                            }
                                        }
                                    }),
                                Forms\Components\Select::make('service_id')
                                    ->label('Service')
                                    ->relationship('service', 'id')
                                    ->getOptionLabelFromRecordUsing(
                                        fn(\App\Models\Service $record) =>
                                        $record->package->name . ' - ' . $record->customer->name
                                    )
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ]),

                Forms\Components\Section::make('Kategori & Prioritas')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('priority_id')
                                    ->label('Prioritas')
                                    ->relationship('priority', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('Assignment & Contact')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('assigned_to')
                                    ->label('Ditugaskan ke')
                                    ->relationship('assignedTo', 'name')
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Select::make('contact_method')
                                    ->label('Metode Kontak')
                                    ->options([
                                        'email' => 'Email',
                                        'phone' => 'Telepon',
                                        'whatsapp' => 'WhatsApp',
                                        'in_person' => 'Langsung',
                                    ])
                                    ->default('email'),
                                Forms\Components\TextInput::make('contact_value')
                                    ->label('Kontak')
                                    ->placeholder('Email atau nomor telepon'),
                            ]),
                    ]),

                Forms\Components\Section::make('Technical Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('location')
                                    ->label('Lokasi'),
                                Forms\Components\Toggle::make('requires_field_visit')
                                    ->label('Perlu Kunjungan Lapangan'),
                            ]),
                        Forms\Components\DateTimePicker::make('scheduled_visit_at')
                            ->label('Jadwal Kunjungan')
                            ->visible(fn(Forms\Get $get) => $get('requires_field_visit')),
                        Forms\Components\KeyValue::make('technical_details')
                            ->label('Detail Teknis')
                            ->keyLabel('Parameter')
                            ->valueLabel('Nilai'),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Billing & Cost')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('estimated_cost')
                                    ->label('Estimasi Biaya')
                                    ->numeric()
                                    ->prefix('Rp'),
                                Forms\Components\Toggle::make('is_billable')
                                    ->label('Dapat Ditagih'),
                                Forms\Components\Toggle::make('is_warranty')
                                    ->label('Garansi'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('No. Ticket')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn($record) => match ($record->category->name) {
                        'Gangguan Internet' => 'primary',
                        'Instalasi Baru' => 'success',
                        'Tagihan & Pembayaran' => 'warning',
                        'Upgrade/Downgrade Paket' => 'info',
                        'Pemutusan Layanan' => 'secondary',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('priority.name')
                    ->label('Prioritas')
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
                        'open' => 'Buka',
                        'in_progress' => 'Proses',
                        'pending_customer' => 'Tunggu Customer',
                        'pending_vendor' => 'Tunggu Vendor',
                        'resolved' => 'Selesai',
                        'closed' => 'Ditutup',
                        'cancelled' => 'Batal',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Ditugaskan')
                    ->placeholder('Belum ditugaskan'),
                Tables\Columns\IconColumn::make('is_escalated')
                    ->label('Eskalasi')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('danger')
                    ->falseColor('secondary'),
                Tables\Columns\TextColumn::make('sla_due_at')
                    ->label('SLA Due')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn($record) => $record->isOverdue() ? 'danger' : 'primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Buka',
                        'in_progress' => 'Dalam Proses',
                        'pending_customer' => 'Menunggu Customer',
                        'pending_vendor' => 'Menunggu Vendor',
                        'resolved' => 'Selesai',
                        'closed' => 'Ditutup',
                        'cancelled' => 'Dibatalkan',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Kategori'),
                Tables\Filters\SelectFilter::make('priority')
                    ->relationship('priority', 'name')
                    ->label('Prioritas'),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->relationship('assignedTo', 'name')
                    ->label('Ditugaskan ke'),
                Tables\Filters\Filter::make('overdue')
                    ->label('Terlambat')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('sla_due_at', '<', now())
                            ->whereNotIn('status', ['resolved', 'closed', 'cancelled'])
                    ),
                Tables\Filters\Filter::make('escalated')
                    ->label('Tereskalasi')
                    ->query(fn(Builder $query): Builder => $query->where('is_escalated', true)),
                Tables\Filters\Filter::make('my_tickets')
                    ->label('Ticket Saya')
                    ->query(fn(Builder $query): Builder => $query->where('assigned_to', auth()->id())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('assign')
                    ->label('Tugaskan')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('assigned_to')
                            ->label('Tugaskan ke')
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (array $data, Ticket $record): void {
                        $record->update(['assigned_to' => $data['assigned_to']]);

                        Notification::make()
                            ->title('Ticket berhasil ditugaskan')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('assign_bulk')
                        ->label('Tugaskan Massal')
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Forms\Components\Select::make('assigned_to')
                                ->label('Tugaskan ke')
                                ->relationship('assignedTo', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $record) {
                                $record->update(['assigned_to' => $data['assigned_to']]);
                            }

                            Notification::make()
                                ->title('Tickets berhasil ditugaskan')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
            RelationManagers\AttachmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view' => Pages\ViewTicket::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
