<?php

namespace App\Filament\Resources\OdpResource\Pages;

use App\Filament\Resources\OdpResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;

class ViewOdp extends ViewRecord
{
    protected static string $resource = OdpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Dasar')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama ODP'),
                        TextEntry::make('code')
                            ->label('Kode ODP'),
                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Lokasi')
                    ->schema([
                        TextEntry::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        TextEntry::make('area')
                            ->label('Kelurahan'),
                        TextEntry::make('district')
                            ->label('Kecamatan'),
                        TextEntry::make('latitude')
                            ->label('Latitude'),
                        TextEntry::make('longitude')
                            ->label('Longitude'),
                    ])->columns(2),

                Section::make('Visualisasi Port')
                    ->schema([
                        ViewEntry::make('port_visualization')
                            ->label('')
                            ->view('filament.resources.odp.port-visualization')
                            ->columnSpanFull(),
                    ]),

                Section::make('Spesifikasi Teknis')
                    ->schema([
                        TextEntry::make('odp_type')
                            ->label('Tipe ODP')
                            ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),
                        TextEntry::make('total_ports')
                            ->label('Total Port'),
                        TextEntry::make('used_ports')
                            ->label('Port Terpakai'),
                        TextEntry::make('available_ports')
                            ->label('Port Tersedia'),
                        TextEntry::make('manufacturer')
                            ->label('Manufacturer'),
                        TextEntry::make('model')
                            ->label('Model'),
                    ])->columns(3),

                Section::make('Informasi Jaringan')
                    ->schema([
                        TextEntry::make('feeder_cable')
                            ->label('Kabel Feeder'),
                        TextEntry::make('fiber_count')
                            ->label('Jumlah Fiber'),
                        TextEntry::make('splitter_ratio')
                            ->label('Rasio Splitter'),
                    ])->columns(3),

                Section::make('Status & Kondisi')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'gray',
                                'maintenance' => 'warning',
                                'damaged' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('condition')
                            ->label('Kondisi')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'excellent' => 'success',
                                'good' => 'success',
                                'fair' => 'warning',
                                'poor' => 'danger',
                                'damaged' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('installation_date')
                            ->label('Tanggal Instalasi')
                            ->date(),
                        TextEntry::make('last_maintenance')
                            ->label('Maintenance Terakhir')
                            ->date()
                            ->placeholder('Belum pernah'),
                    ])->columns(2),

                Section::make('Catatan')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull()
                            ->placeholder('Tidak ada catatan'),
                    ]),
            ]);
    }
}
