<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Filters\SelectFilter;

class RecentPaymentsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Riwayat Pembayaran Terbaru';

    protected function getTableHeading(): string
    {
        $currentMonth = now()->format('F Y');
        $totalThisMonth = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        return 'Riwayat Pembayaran Terbaru - ' . $currentMonth . ' (Total: Rp ' . number_format($totalThisMonth, 0, ',', '.') . ')';
    }

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->with(['service.customer', 'invoice.customer'])
                    ->latest('payment_date')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer_number')
                    ->label('No. Pelanggan')
                    ->getStateUsing(function ($record) {
                        return $record->invoice?->customer?->customer_number
                            ?? $record->service?->customer?->customer_number
                            ?? 'N/A';
                    })
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->getStateUsing(function ($record) {
                        return $record->invoice?->customer?->name
                            ?? $record->service?->customer?->name
                            ?? 'N/A';
                    })
                    ->searchable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->weight('bold')
                    ->color('success')
                    ->summarize([
                        Sum::make()
                            ->money('IDR')
                            ->label('Total Pembayaran'),
                        Count::make()
                            ->label('Jumlah Transaksi')
                    ]),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'cash' => 'success',
                            'transfer' => 'info',
                            'credit_card' => 'warning',
                            'e_wallet' => 'primary',
                            default => 'gray'
                        };
                    }),

                Tables\Columns\TextColumn::make('payment_period')
                    ->label('Periode Bulan')
                    ->getStateUsing(function ($record) {
                        $months = [
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
                        $month = $record->payment_date->month;
                        $year = $record->payment_date->year;
                        return $months[$month] . ' ' . $year;
                    })
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Tanggal')
                    ->date('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_badge')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if ($record->invoice && $record->payment_date > $record->invoice->due_date) {
                            return 'Terlambat';
                        }
                        return 'Tepat Waktu';
                    })
                    ->color(function ($state) {
                        return $state === 'Tepat Waktu' ? 'success' : 'danger';
                    }),
            ])
            ->filters([
                SelectFilter::make('payment_month')
                    ->label('Periode Bulan')
                    ->options([
                        '1' => 'Januari',
                        '2' => 'Februari',
                        '3' => 'Maret',
                        '4' => 'April',
                        '5' => 'Mei',
                        '6' => 'Juni',
                        '7' => 'Juli',
                        '8' => 'Agustus',
                        '9' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value']) {
                            return $query->whereMonth('payment_date', $data['value']);
                        }
                        return $query;
                    }),

                SelectFilter::make('payment_year')
                    ->label('Tahun')
                    ->options([
                        '2023' => '2023',
                        '2024' => '2024',
                        '2025' => '2025',
                        '2026' => '2026',
                    ])
                    ->default(now()->year)
                    ->query(function ($query, array $data) {
                        if ($data['value']) {
                            return $query->whereYear('payment_date', $data['value']);
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-m-eye')
                    ->url(fn(Payment $record): string => route('filament.admin.resources.payments.edit', $record))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false)
            ->defaultSort('payment_date', 'desc');
    }
}
