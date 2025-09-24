<?php

namespace App\Filament\Resources\CustomerReportResource\Pages;

use App\Filament\Resources\CustomerReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewCustomerReport extends ViewRecord
{
    protected static string $resource = CustomerReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->url(fn() => route('filament.admin.resources.customers.edit', $this->record)),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pelanggan')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nama Lengkap'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('phone')
                            ->label('Telepon'),
                        Infolists\Components\TextEntry::make('address')
                            ->label('Alamat'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'danger',
                                'suspended' => 'warning',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Terdaftar')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Statistik Layanan')
                    ->schema([
                        Infolists\Components\TextEntry::make('services_count')
                            ->label('Jumlah Layanan')
                            ->getStateUsing(fn($record) => $record->services()->count())
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('active_services')
                            ->label('Layanan Aktif')
                            ->getStateUsing(fn($record) => $record->services()->where('status', 'active')->count())
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Statistik Pembayaran')
                    ->schema([
                        Infolists\Components\TextEntry::make('total_payments')
                            ->label('Total Pembayaran')
                            ->getStateUsing(fn($record) => $record->payments()->count())
                            ->badge()
                            ->color('warning'),
                        Infolists\Components\TextEntry::make('total_amount')
                            ->label('Total Dibayar')
                            ->getStateUsing(fn($record) => 'Rp ' . number_format($record->payments()->sum('amount'), 0, ',', '.'))
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('last_payment')
                            ->label('Pembayaran Terakhir')
                            ->getStateUsing(function ($record) {
                                $last = $record->payments()->latest()->first();
                                return $last ? $last->created_at->format('d/m/Y H:i') : 'Belum ada pembayaran';
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Riwayat Pembayaran Terbaru')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('payments')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('periode')
                                    ->label('Periode')
                                    ->badge()
                                    ->color('info'),
                                Infolists\Components\TextEntry::make('payment_date')
                                    ->label('Tanggal')
                                    ->date('d/m/Y'),
                                Infolists\Components\TextEntry::make('payment_method')
                                    ->label('Metode'),
                                Infolists\Components\TextEntry::make('amount')
                                    ->label('Jumlah')
                                    ->money('IDR'),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('keterangan_pembayaran')
                                    ->label('Keterangan')
                                    ->getStateUsing(function ($record) {
                                        $monthNames = [
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
                                            12 => 'Desember'
                                        ];

                                        if ($record->notes) {
                                            return $record->notes;
                                        }

                                        $monthName = $monthNames[$record->month] ?? 'Unknown';
                                        return "Pembayaran bulan {$monthName}";
                                    }),
                            ])
                            ->columns(6)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn($record) => $record->payments()->exists()),
            ]);
    }
}
