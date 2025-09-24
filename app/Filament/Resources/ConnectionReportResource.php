<?php

namespace App\Filament\Resources;

use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ConnectionReportResource\Pages;

class ConnectionReportResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Koneksi';

    protected static ?string $modelLabel = 'Laporan Koneksi';

    protected static ?string $pluralModelLabel = 'Laporan Koneksi';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.customer_number')
                    ->label('No. Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.phone')
                    ->label('Phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.address')
                    ->label('Address')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('odp.name')
                    ->label('ODP')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('odp.code')
                    ->label('ODP Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('odp_port')
                    ->label('Port')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('package.name')
                    ->label('Package')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fiber_cable_color')
                    ->label('Fiber Color')
                    ->badge()
                    ->color(function ($state): string {
                        return match (strtolower($state ?? '')) {
                            'red' => 'danger',
                            'green' => 'success',
                            'blue' => 'info',
                            'yellow' => 'warning',
                            'orange' => 'warning',
                            'purple' => 'primary',
                            default => 'gray',
                        };
                    }),
                Tables\Columns\TextColumn::make('signal_strength')
                    ->label('Signal (dBm)')
                    ->sortable()
                    ->alignCenter()
                    ->color(function ($state): string {
                        if (!$state) return 'gray';
                        return $state >= -20 ? 'success' : ($state >= -30 ? 'warning' : 'danger');
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(function ($state): string {
                        return match ($state) {
                            'active' => 'success',
                            'inactive' => 'gray',
                            'suspended' => 'warning',
                            default => 'gray',
                        };
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('odp.area')
                    ->label('Area')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('odp.district')
                    ->label('District')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('odp_id')
                    ->label('ODP')
                    ->relationship('odp', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
                SelectFilter::make('package_id')
                    ->label('Package')
                    ->relationship('package', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('signal_strength')
                    ->form([
                        Forms\Components\Select::make('signal_range')
                            ->label('Signal Strength Range')
                            ->options([
                                'excellent' => 'Excellent (≥ -20 dBm)',
                                'good' => 'Good (-20 to -30 dBm)',
                                'poor' => 'Poor (< -30 dBm)',
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['signal_range'],
                            fn(Builder $query, $range): Builder => match ($range) {
                                'excellent' => $query->where('signal_strength', '>=', -20),
                                'good' => $query->whereBetween('signal_strength', [-30, -20]),
                                'poor' => $query->where('signal_strength', '<', -30),
                                default => $query,
                            }
                        );
                    }),
                Filter::make('area')
                    ->form([
                        Forms\Components\TextInput::make('area')
                            ->label('Area/Kelurahan')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['area'],
                            fn(Builder $query, $area): Builder => $query->whereHas(
                                'odp',
                                fn(Builder $query) => $query->where('area', 'like', "%{$area}%")
                            )
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn(Service $record): string => route('filament.admin.resources.customers.edit', $record->customer)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('customer.name')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConnectionReports::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
