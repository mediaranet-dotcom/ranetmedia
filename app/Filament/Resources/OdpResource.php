<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OdpResource\Pages;
use App\Models\Odp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// Temporarily disable Google Maps imports
// use Cheesegrits\FilamentGoogleMaps\Fields\Map;
// use Cheesegrits\FilamentGoogleMaps\Fields\Geocomplete;

class OdpResource extends Resource
{
    protected static ?string $model = Odp::class;

    protected static ?string $navigationIcon = 'heroicon-o-signal';

    protected static ?string $navigationLabel = 'Manajemen ODP';

    protected static ?string $modelLabel = 'ODP';

    protected static ?string $pluralModelLabel = 'ODP';

    protected static ?string $navigationGroup = 'Infrastruktur Jaringan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama ODP')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('ODP-001')
                            ->helperText('Identitas unik ODP'),
                        Forms\Components\TextInput::make('code')
                            ->label('Kode ODP')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('ODP001')
                            ->helperText('Kode internal untuk ODP'),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->placeholder('Deskripsi lokasi dan tujuan ODP'),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Lokasi')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->columnSpanFull()
                            ->placeholder('Alamat lengkap lokasi ODP')
                            ->helperText('Masukkan alamat lengkap lokasi ODP'),

                        Forms\Components\ViewField::make('map_picker')
                            ->label('Peta Lokasi')
                            ->view('filament.forms.components.leaflet-map')
                            ->columnSpanFull()
                            ->afterStateHydrated(function ($component, $get) {
                                $component->state([
                                    'latitude' => $get('latitude') ?? -6.200000,
                                    'longitude' => $get('longitude') ?? 106.816666,
                                ]);
                            }),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->step(0.00000001)
                                    ->placeholder('-6.200000')
                                    ->helperText('Klik pada peta untuk mengatur koordinat')
                                    ->live(),
                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->step(0.00000001)
                                    ->placeholder('106.816666')
                                    ->helperText('Klik pada peta untuk mengatur koordinat')
                                    ->live(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('area')
                                    ->label('Kelurahan/Desa')
                                    ->maxLength(255)
                                    ->placeholder('Kelurahan/Desa'),
                                Forms\Components\TextInput::make('district')
                                    ->label('Kecamatan')
                                    ->maxLength(255)
                                    ->placeholder('Kecamatan'),
                            ]),
                    ]),

                Forms\Components\Section::make('Spesifikasi Teknis')
                    ->schema([
                        Forms\Components\Select::make('odp_type')
                            ->label('Tipe ODP')
                            ->required()
                            ->options([
                                '8_port' => '8 Port',
                                '16_port' => '16 Port',
                                '32_port' => '32 Port',
                                '64_port' => '64 Port',
                            ])
                            ->default('8_port')
                            ->reactive()
                            ->afterStateUpdated(
                                fn($state, callable $set) =>
                                $set('total_ports', match ($state) {
                                    '8_port' => 8,
                                    '16_port' => 16,
                                    '32_port' => 32,
                                    '64_port' => 64,
                                    default => 8,
                                })
                            ),
                        Forms\Components\TextInput::make('total_ports')
                            ->label('Total Port')
                            ->required()
                            ->numeric()
                            ->default(8)
                            ->disabled(),
                        Forms\Components\TextInput::make('used_ports')
                            ->label('Port Terpakai')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Dihitung otomatis dari layanan terhubung'),
                        Forms\Components\TextInput::make('available_ports')
                            ->label('Port Tersedia')
                            ->numeric()
                            ->default(8)
                            ->disabled()
                            ->helperText('Dihitung otomatis: total - terpakai'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama ODP')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('area')
                    ->label('Kelurahan')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('district')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('odp_type')
                    ->label('Tipe')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('port_utilization')
                    ->label('Penggunaan Port')
                    ->formatStateUsing(
                        fn(Odp $record): string =>
                        "{$record->used_ports}/{$record->total_ports} ({$record->getUtilizationPercentage()}%)"
                    )
                    ->color(
                        fn(Odp $record): string =>
                        $record->isNearCapacity() ? 'warning' : 'success'
                    ),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'secondary',
                        'maintenance' => 'warning',
                        'damaged' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'maintenance' => 'Pemeliharaan',
                        'damaged' => 'Rusak',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'excellent' => 'success',
                        'good' => 'info',
                        'fair' => 'warning',
                        'poor' => 'danger',
                        'damaged' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'excellent' => 'Sangat Baik',
                        'good' => 'Baik',
                        'fair' => 'Cukup',
                        'poor' => 'Buruk',
                        'damaged' => 'Rusak',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('installation_date')
                    ->label('Tanggal Instalasi')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('last_maintenance')
                    ->label('Pemeliharaan Terakhir')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Belum Pernah'),
                Tables\Columns\TextColumn::make('services_count')
                    ->label('Layanan Terhubung')
                    ->counts('services')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'maintenance' => 'Maintenance',
                        'damaged' => 'Damaged',
                    ]),
                Tables\Filters\SelectFilter::make('condition')
                    ->options([
                        'excellent' => 'Excellent',
                        'good' => 'Good',
                        'fair' => 'Fair',
                        'poor' => 'Poor',
                        'damaged' => 'Damaged',
                    ]),
                Tables\Filters\SelectFilter::make('odp_type')
                    ->label('ODP Type')
                    ->options([
                        '8_port' => '8 Port',
                        '16_port' => '16 Port',
                        '32_port' => '32 Port',
                        '64_port' => '64 Port',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListOdps::route('/'),
            'create' => Pages\CreateOdp::route('/create'),
            'view' => Pages\ViewOdp::route('/{record}'),
            'edit' => Pages\EditOdp::route('/{record}/edit'),
        ];
    }
}
