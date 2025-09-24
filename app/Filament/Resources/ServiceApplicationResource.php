<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceApplicationResource\Pages;
use App\Filament\Resources\ServiceApplicationResource\RelationManagers;
use App\Models\ServiceApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceApplicationResource extends Resource
{
    protected static ?string $model = ServiceApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Pengajuan Layanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->label('Pelanggan')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('package_id')
                    ->label('Paket')
                    ->relationship('package', 'name')
                    ->required(),
                Forms\Components\Textarea::make('installation_address')
                    ->label('Alamat Instalasi')
                    ->rows(3),
                Forms\Components\Textarea::make('installation_notes')
                    ->label('Catatan Instalasi')
                    ->rows(3),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.customer_number')
                    ->label('No. Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('package.name')
                    ->label('Paket')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update(['status' => 'approved']);

                        // Optional: Create active service automatically
                        \App\Models\Service::create([
                            'customer_id' => $record->customer_id,
                            'package_id' => $record->package_id,
                            'ip_address' => null, // Will be set later
                            'router_name' => null, // Will be set later
                            'start_date' => now(),
                            'status' => 'active',
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Service Application Approved')
                            ->body('Service has been created and activated.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);

                        \Filament\Notifications\Notification::make()
                            ->title('Service Application Rejected')
                            ->danger()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListServiceApplications::route('/'),
            'create' => Pages\CreateServiceApplication::route('/create'),
            'edit' => Pages\EditServiceApplication::route('/{record}/edit'),
        ];
    }
}
