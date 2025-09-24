<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pelanggan';

    protected static ?string $modelLabel = 'Pelanggan';

    protected static ?string $pluralModelLabel = 'Pelanggan';

    protected static ?string $navigationGroup = 'Manajemen Pelanggan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('customer_number')
                    ->label('Nomor Pelanggan')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Auto-generated')
                    ->helperText('Nomor pelanggan akan dibuat otomatis'),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->required()
                    ->maxLength(20),
                Forms\Components\Textarea::make('address')
                    ->label('Alamat Jalan/Rumah')
                    ->required()
                    ->placeholder('Contoh: Jl. Merdeka No. 123, Blok A')
                    ->helperText('Alamat jalan, nomor rumah, atau patokan utama')
                    ->columnSpanFull(),

                // Section Alamat Detail
                Forms\Components\Section::make('Detail Alamat Administratif')
                    ->description('Isi detail wilayah administratif untuk memudahkan manajemen coverage area')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('province')
                                    ->label('Provinsi')
                                    ->placeholder('Contoh: Jawa Tengah')
                                    ->datalist([
                                        'Aceh',
                                        'Sumatera Utara',
                                        'Sumatera Barat',
                                        'Riau',
                                        'Jambi',
                                        'Sumatera Selatan',
                                        'Bengkulu',
                                        'Lampung',
                                        'Kepulauan Bangka Belitung',
                                        'Kepulauan Riau',
                                        'DKI Jakarta',
                                        'Jawa Barat',
                                        'Jawa Tengah',
                                        'DI Yogyakarta',
                                        'Jawa Timur',
                                        'Banten',
                                        'Bali',
                                        'Nusa Tenggara Barat',
                                        'Nusa Tenggara Timur',
                                        'Kalimantan Barat',
                                        'Kalimantan Tengah',
                                        'Kalimantan Selatan',
                                        'Kalimantan Timur',
                                        'Kalimantan Utara',
                                        'Sulawesi Utara',
                                        'Sulawesi Tengah',
                                        'Sulawesi Selatan',
                                        'Sulawesi Tenggara',
                                        'Gorontalo',
                                        'Sulawesi Barat',
                                        'Maluku',
                                        'Maluku Utara',
                                        'Papua',
                                        'Papua Barat',
                                    ]),
                                Forms\Components\TextInput::make('regency')
                                    ->label('Kabupaten/Kota')
                                    ->placeholder('Contoh: Kabupaten Semarang'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('district')
                                    ->label('Kecamatan')
                                    ->required()
                                    ->placeholder('Contoh: Ungaran Barat')
                                    ->helperText('⚠️ Wajib diisi untuk manajemen coverage area'),
                                Forms\Components\TextInput::make('village')
                                    ->label('Desa/Kelurahan')
                                    ->required()
                                    ->placeholder('Contoh: Lerep')
                                    ->helperText('⚠️ Wajib diisi untuk manajemen coverage area'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('hamlet')
                                    ->label('Dusun/Lingkungan')
                                    ->placeholder('Contoh: Krajan, Tengah')
                                    ->helperText('Opsional, untuk area yang lebih spesifik'),
                                Forms\Components\TextInput::make('rt')
                                    ->label('RT')
                                    ->placeholder('01')
                                    ->maxLength(3),
                                Forms\Components\TextInput::make('rw')
                                    ->label('RW')
                                    ->placeholder('05')
                                    ->maxLength(3),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('postal_code')
                                    ->label('Kode Pos')
                                    ->placeholder('50552')
                                    ->maxLength(5)
                                    ->numeric(),
                                Forms\Components\Textarea::make('address_notes')
                                    ->label('Catatan Alamat')
                                    ->placeholder('Contoh: Dekat Masjid Al-Ikhlas, sebelah warung Bu Sari')
                                    ->helperText('Patokan atau catatan khusus untuk memudahkan teknisi')
                                    ->rows(2),
                            ]),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('identity_number')
                    ->label('Nomor Identitas (KTP/SIM)')
                    ->maxLength(50),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                    ])
                    ->required()
                    ->default('active'),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_number')
                    ->label('No. Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nomor pelanggan disalin!')
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('coverage_area')
                    ->label('Wilayah Coverage')
                    ->getStateUsing(fn($record) => $record->coverage_area)
                    ->badge()
                    ->color('info')
                    ->searchable(['district', 'village'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('identity_number')
                    ->label('No. Identitas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'suspended' => 'warning',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('district')
                    ->label('Filter Kecamatan')
                    ->options(function () {
                        return \App\Models\Customer::whereNotNull('district')
                            ->distinct()
                            ->pluck('district', 'district')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('village')
                    ->label('Filter Desa/Kelurahan')
                    ->options(function () {
                        return \App\Models\Customer::whereNotNull('village')
                            ->distinct()
                            ->pluck('village', 'village')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('hamlet')
                    ->label('Filter Dusun')
                    ->options(function () {
                        return \App\Models\Customer::whereNotNull('hamlet')
                            ->distinct()
                            ->pluck('hamlet', 'hamlet')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
