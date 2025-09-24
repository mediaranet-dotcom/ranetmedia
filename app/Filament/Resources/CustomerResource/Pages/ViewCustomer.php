<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pelanggan')
                    ->schema([
                        Infolists\Components\TextEntry::make('customer_number')
                            ->label('Nomor Pelanggan')
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nama Lengkap'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('phone')
                            ->label('Nomor Telepon')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('identity_number')
                            ->label('Nomor KTP/SIM')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'danger',
                                'suspended' => 'warning',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Alamat Lengkap')
                    ->schema([
                        Infolists\Components\TextEntry::make('address')
                            ->label('Alamat Jalan/Rumah'),
                        Infolists\Components\TextEntry::make('hamlet')
                            ->label('Dusun'),
                        Infolists\Components\TextEntry::make('rt')
                            ->label('RT'),
                        Infolists\Components\TextEntry::make('rw')
                            ->label('RW'),
                        Infolists\Components\TextEntry::make('village')
                            ->label('Desa/Kelurahan'),
                        Infolists\Components\TextEntry::make('district')
                            ->label('Kecamatan'),
                        Infolists\Components\TextEntry::make('regency')
                            ->label('Kabupaten/Kota'),
                        Infolists\Components\TextEntry::make('province')
                            ->label('Provinsi'),
                        Infolists\Components\TextEntry::make('postal_code')
                            ->label('Kode Pos'),
                        Infolists\Components\TextEntry::make('address_notes')
                            ->label('Catatan Alamat')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Pengajuan Layanan')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('serviceApplications')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('package.name')
                                    ->label('Paket')
                                    ->badge()
                                    ->color('info'),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'approved' => 'info',
                                        'rejected' => 'danger',
                                        'completed' => 'success',
                                        default => 'gray',
                                    }),
                                Infolists\Components\TextEntry::make('installation_notes')
                                    ->label('Catatan Instalasi')
                                    ->placeholder('Tidak ada catatan'),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Tanggal Pengajuan')
                                    ->dateTime(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->serviceApplications->count() > 0),

                Infolists\Components\Section::make('Layanan Aktif')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('services')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('package.name')
                                    ->label('Paket')
                                    ->badge()
                                    ->color('success'),
                                Infolists\Components\TextEntry::make('ip_address')
                                    ->label('IP Address')
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('router_name')
                                    ->label('Router'),
                                Infolists\Components\TextEntry::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->date(),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'active' => 'success',
                                        'inactive' => 'danger',
                                        'suspended' => 'warning',
                                        default => 'gray',
                                    }),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->services->count() > 0),
            ]);
    }
}
