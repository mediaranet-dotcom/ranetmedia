<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AutoInvoiceResource\Pages;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Services\EmailService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class AutoInvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Auto Invoice';

    protected static ?string $modelLabel = 'Auto Invoice';

    protected static ?string $pluralModelLabel = 'Auto Invoice Management';

    protected static ?string $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'auto-invoices';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Generate Invoices')
                    ->description('Generate invoices automatically for active services')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('month')
                                    ->label('Bulan')
                                    ->options([
                                        1 => 'Januari',
                                        2 => 'Februari',
                                        3 => 'Maret',
                                        4 => 'April',
                                        5 => 'Mei',
                                        6 => 'Juni',
                                        7 => 'Juli',
                                        8 => 'Agustus',
                                        9 => 'September',
                                        10 => 'Oktober',
                                        11 => 'November',
                                        12 => 'Desember',
                                    ])
                                    ->default(now()->month)
                                    ->required(),
                                Forms\Components\TextInput::make('year')
                                    ->label('Tahun')
                                    ->numeric()
                                    ->default(now()->year)
                                    ->required(),
                            ]),
                        Forms\Components\Toggle::make('send_email')
                            ->label('Kirim Email ke Customer')
                            ->default(true)
                            ->helperText('Otomatis kirim invoice via email setelah generate'),
                        Forms\Components\Toggle::make('dry_run')
                            ->label('Dry Run (Preview Only)')
                            ->default(false)
                            ->helperText('Preview saja tanpa benar-benar membuat invoice'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.package.name')
                    ->label('Paket')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($record) => $record->isOverdue() ? 'danger' : 'primary'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'secondary',
                        'sent' => 'info',
                        'paid' => 'success',
                        'partial_paid' => 'warning',
                        'overdue' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'sent' => 'Terkirim',
                        'paid' => 'Lunas',
                        'partial_paid' => 'Sebagian',
                        'overdue' => 'Terlambat',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Dikirim')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
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
                        'draft' => 'Draft',
                        'sent' => 'Terkirim',
                        'paid' => 'Lunas',
                        'partial_paid' => 'Sebagian',
                        'overdue' => 'Terlambat',
                        'cancelled' => 'Dibatalkan',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->label('Terlambat')
                    ->query(fn(Builder $query): Builder => $query->where('due_date', '<', now())->where('status', '!=', 'paid')),
                Tables\Filters\Filter::make('this_month')
                    ->label('Bulan Ini')
                    ->query(fn(Builder $query): Builder => $query->whereMonth('created_at', now()->month)),
            ])
            ->actions([
                Tables\Actions\Action::make('send_email')
                    ->label('Kirim Email')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->visible(fn($record) => $record->customer->email && !$record->sent_at)
                    ->action(function ($record) {
                        try {
                            $emailService = app(EmailService::class);
                            $emailService->sendInvoiceEmail($record);

                            Notification::make()
                                ->title('Email Terkirim')
                                ->body("Invoice berhasil dikirim ke {$record->customer->name}")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Kirim Email')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('send_reminder')
                    ->label('Kirim Pengingat')
                    ->icon('heroicon-o-bell')
                    ->color('warning')
                    ->visible(fn($record) => $record->customer->email && $record->isOverdue())
                    ->action(function ($record) {
                        try {
                            $emailService = app(EmailService::class);
                            $emailService->sendPaymentReminder($record);

                            Notification::make()
                                ->title('Pengingat Terkirim')
                                ->body("Pengingat pembayaran berhasil dikirim ke {$record->customer->name}")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Kirim Pengingat')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('send_bulk_email')
                    ->label('Kirim Email Massal')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->action(function ($records) {
                        $emailService = app(EmailService::class);
                        $sent = 0;
                        $failed = 0;

                        foreach ($records as $record) {
                            if ($record->customer->email && !$record->sent_at) {
                                try {
                                    $emailService->sendInvoiceEmail($record);
                                    $sent++;
                                } catch (\Exception $e) {
                                    $failed++;
                                }
                            }
                        }

                        Notification::make()
                            ->title('Email Massal Selesai')
                            ->body("Berhasil: {$sent}, Gagal: {$failed}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\BulkAction::make('send_bulk_reminder')
                    ->label('Kirim Pengingat Massal')
                    ->icon('heroicon-o-bell')
                    ->color('warning')
                    ->action(function ($records) {
                        $emailService = app(EmailService::class);
                        $sent = 0;
                        $failed = 0;

                        foreach ($records as $record) {
                            if ($record->customer->email && $record->isOverdue()) {
                                try {
                                    $emailService->sendPaymentReminder($record);
                                    $sent++;
                                } catch (\Exception $e) {
                                    $failed++;
                                }
                            }
                        }

                        Notification::make()
                            ->title('Pengingat Massal Selesai')
                            ->body("Berhasil: {$sent}, Gagal: {$failed}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAutoInvoices::route('/'),
            'create' => Pages\CreateAutoInvoice::route('/create'),
            'view' => Pages\ViewAutoInvoice::route('/{record}'),
            'edit' => Pages\EditAutoInvoice::route('/{record}/edit'),
        ];
    }
}
