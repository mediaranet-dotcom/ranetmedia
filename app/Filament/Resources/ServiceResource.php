<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Layanan';

    protected static ?string $modelLabel = 'Layanan';

    protected static ?string $pluralModelLabel = 'Layanan';

    protected static ?string $navigationGroup = 'Manajemen Layanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->label('Pelanggan')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('package_id')
                    ->label('Paket Layanan')
                    ->relationship('package', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('network_type')
                    ->label('Jenis Jaringan')
                    ->options([
                        'odp' => 'Fiber Optik (ODP)',
                        'wireless' => 'Wireless/Radio',
                        'htb' => 'Hotspot (HTB)',
                    ])
                    ->required()
                    ->default('odp')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Clear all network-specific fields when changing network type
                        if ($state !== 'odp') {
                            $set('odp_id', null);
                            $set('odp_port', null);
                            $set('fiber_cable_color', null);
                            $set('signal_strength', null);
                        }
                        if ($state !== 'wireless') {
                            $set('wireless_equipment', null);
                            $set('antenna_type', null);
                            $set('frequency', null);
                        }
                        if ($state !== 'htb') {
                            $set('htb_server', null);
                            $set('access_point', null);
                        }
                    })
                    ->helperText('Pilih jenis infrastruktur jaringan yang digunakan'),
                // === ODP SECTION (Only for Fiber) ===
                Forms\Components\Section::make('Konfigurasi Fiber Optik (ODP)')
                    ->schema([
                        Forms\Components\Select::make('odp_id')
                            ->label('Pilih ODP')
                            ->relationship('odp', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $odp = \App\Models\Odp::find($state);
                                    if ($odp) {
                                        // Auto-suggest next available port
                                        $usedPorts = \App\Models\Service::where('odp_id', $state)
                                            ->whereNotNull('odp_port')
                                            ->pluck('odp_port')
                                            ->toArray();

                                        for ($i = 1; $i <= $odp->total_ports; $i++) {
                                            if (!in_array($i, $usedPorts)) {
                                                $set('odp_port', $i);
                                                break;
                                            }
                                        }
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('odp_port')
                            ->label('Port ODP')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(64)
                            ->helperText('Nomor port di ODP (1-64)'),
                        Forms\Components\Select::make('fiber_cable_color')
                            ->label('Warna Kabel Fiber')
                            ->options([
                                'blue' => 'Biru',
                                'orange' => 'Oranye',
                                'green' => 'Hijau',
                                'brown' => 'Coklat',
                                'slate' => 'Abu-abu',
                                'white' => 'Putih',
                                'red' => 'Merah',
                                'black' => 'Hitam',
                                'yellow' => 'Kuning',
                                'violet' => 'Ungu',
                                'rose' => 'Pink',
                                'aqua' => 'Biru Muda',
                            ]),
                        Forms\Components\TextInput::make('signal_strength')
                            ->label('Kekuatan Sinyal (dBm)')
                            ->numeric()
                            ->step(0.01)
                            ->placeholder('-15.50')
                            ->helperText('Level daya optik dalam dBm'),
                    ])
                    ->columns(2)
                    ->visible(fn(callable $get) => $get('network_type') === 'odp'),
                // === WIRELESS SECTION ===
                Forms\Components\Section::make('Konfigurasi Wireless/Radio')
                    ->schema([
                        Forms\Components\TextInput::make('wireless_equipment')
                            ->label('Perangkat Wireless')
                            ->placeholder('Ubiquiti NanoStation M5, MikroTik SXT, dll')
                            ->helperText('Jenis dan model perangkat wireless yang digunakan'),
                        Forms\Components\Select::make('antenna_type')
                            ->label('Jenis Antena')
                            ->options([
                                'omni' => 'Omni (Segala Arah)',
                                'sectoral' => 'Sectoral (Sektoral)',
                                'yagi' => 'Yagi',
                                'panel' => 'Panel',
                                'dish' => 'Dish/Parabola',
                                'integrated' => 'Terintegrasi (Built-in)',
                            ])
                            ->placeholder('Pilih jenis antena'),
                        Forms\Components\TextInput::make('frequency')
                            ->label('Frekuensi (GHz)')
                            ->numeric()
                            ->step(0.01)
                            ->placeholder('2.4 atau 5.8')
                            ->helperText('Frekuensi operasi dalam GHz (contoh: 2.4, 5.8)'),
                    ])
                    ->columns(2)
                    ->visible(fn(callable $get) => $get('network_type') === 'wireless'),

                // === HTB SECTION ===
                Forms\Components\Section::make('Konfigurasi Hotspot (HTB)')
                    ->schema([
                        Forms\Components\TextInput::make('htb_server')
                            ->label('Server HTB')
                            ->placeholder('192.168.1.1 atau nama server')
                            ->helperText('Alamat IP atau nama server HTB/Hotspot'),
                        Forms\Components\TextInput::make('access_point')
                            ->label('Titik Akses (Access Point)')
                            ->placeholder('AP-001, MikroTik hAP, dll')
                            ->helperText('Nama atau model Access Point yang digunakan'),
                    ])
                    ->columns(2)
                    ->visible(fn(callable $get) => $get('network_type') === 'htb'),
                Forms\Components\TextInput::make('ip_address')
                    ->label('Alamat IP')
                    ->ipv4()
                    ->placeholder('192.168.1.100')
                    ->helperText('Alamat IP yang diberikan kepada pelanggan'),
                Forms\Components\TextInput::make('router_name')
                    ->label('Nama Router')
                    ->placeholder('RT-001, MikroTik-001, dll')
                    ->helperText('Nama atau identitas router pelanggan'),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->helperText('Tanggal mulai layanan aktif'),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Tanggal Berakhir')
                    ->helperText('Tanggal berakhir layanan (opsional)'),
                Forms\Components\Select::make('status')
                    ->label('Status Layanan')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                    ])
                    ->required()
                    ->default('active')
                    ->helperText('Status layanan pelanggan'),
                Forms\Components\Select::make('billing_cycle_id')
                    ->label('Siklus Tagihan')
                    ->relationship('billingCycle', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(1) // Default to Monthly billing cycle
                    ->helperText('Pilih siklus tagihan (bulanan, tahunan, dll)'),
                Forms\Components\TextInput::make('billing_day')
                    ->label('Tanggal Tagihan')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(31)
                    ->default(1)
                    ->helperText('Tanggal dalam bulan untuk tagihan (1-31)'),
                Forms\Components\TextInput::make('monthly_fee')
                    ->label('Biaya Bulanan Khusus')
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('Kosongkan untuk menggunakan harga paket'),
                Forms\Components\Toggle::make('auto_billing')
                    ->label('Tagihan Otomatis')
                    ->default(true)
                    ->helperText('Buat tagihan secara otomatis'),
            ]);
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
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('package.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('odp.name')
                    ->label('ODP')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('odp_port')
                    ->label('Port')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('fiber_cable_color')
                    ->label('Cable Color')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'blue' => 'info',
                        'orange' => 'warning',
                        'green' => 'success',
                        'red' => 'danger',
                        default => 'secondary',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('signal_strength')
                    ->label('Signal (dBm)')
                    ->sortable()
                    ->color(
                        fn($state): string =>
                        $state && $state > -20 ? 'success' : ($state && $state > -25 ? 'warning' : 'danger')
                    )
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('router_name')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->placeholder('Ongoing'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'suspended' => 'warning',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
                Tables\Filters\SelectFilter::make('package')
                    ->relationship('package', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
