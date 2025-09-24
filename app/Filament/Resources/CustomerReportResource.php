<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerReportResource\Pages;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use App\Models\Payment;

class CustomerReportResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Laporan Pembayaran';
    protected static ?string $modelLabel = 'Laporan Pembayaran';
    protected static ?string $pluralModelLabel = 'Laporan Pembayaran';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Customer::query()
                    ->withCount('services')
                    ->withSum('payments', 'amount')
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer_number')
                    ->label('Nomor Pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('services_count')
                    ->label('Jumlah Layanan')
                    ->sortable()
                    ->default(0),
                Tables\Columns\TextColumn::make('payments_sum_amount')
                    ->label('Total Pembayaran')
                    ->money('IDR')
                    ->sortable()
                    ->default(0)
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->summarize([
                        Sum::make()
                            ->money('IDR')
                            ->label('Total Keseluruhan')
                    ]),
                Tables\Columns\TextColumn::make('monthly_payment')
                    ->label('Pembayaran Periode Terpilih')
                    ->getStateUsing(function ($record) {
                        // Get selected year and month from request, default to current
                        $selectedYear = request()->get('tableFilters.payment_year.value', now()->year);
                        $selectedMonth = request()->get('tableFilters.payment_month.value', now()->month);

                        $periodPayment = $record->payments()
                            ->whereMonth('payment_date', $selectedMonth)
                            ->whereYear('payment_date', $selectedYear)
                            ->sum('amount');
                        return $periodPayment;
                    })
                    ->money('IDR')
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->summarize([
                        Summarizer::make()
                            ->using(function ($query) {
                                $selectedYear = request()->get('tableFilters.payment_year.value', now()->year);
                                $selectedMonth = request()->get('tableFilters.payment_month.value', now()->month);

                                // Get customer IDs from the current query
                                $customerIds = $query->pluck('id')->toArray();

                                return Payment::whereHas('service', function ($q) use ($customerIds) {
                                    $q->whereIn('customer_id', $customerIds);
                                })
                                    ->whereMonth('payment_date', $selectedMonth)
                                    ->whereYear('payment_date', $selectedYear)
                                    ->sum('amount');
                            })
                            ->money('IDR')
                            ->label('Total Periode')
                    ]),
                Tables\Columns\TextColumn::make('periode_pembayaran')
                    ->label('Periode Pembayaran')
                    ->getStateUsing(function ($record) {
                        $selectedYear = request()->get('tableFilters.payment_year.value', now()->year);
                        $selectedMonth = request()->get('tableFilters.payment_month.value', now()->month);

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

                        return $monthNames[$selectedMonth] . '-' . $selectedYear;
                    })
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('status_pembayaran_periode')
                    ->label('Status Periode')
                    ->getStateUsing(function ($record) {
                        $selectedYear = request()->get('tableFilters.payment_year.value', now()->year);
                        $selectedMonth = request()->get('tableFilters.payment_month.value', now()->month);

                        $hasPayment = $record->payments()
                            ->whereMonth('payments.created_at', $selectedMonth)
                            ->whereYear('payments.created_at', $selectedYear)
                            ->exists();

                        return $hasPayment ? 'Sudah Bayar' : 'Belum Bayar';
                    })
                    ->badge()
                    ->color(fn($state) => $state === 'Sudah Bayar' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('last_payment_date')
                    ->label('Pembayaran Terakhir')
                    ->getStateUsing(function ($record) {
                        $lastPayment = $record->payments()->latest('payments.created_at')->first();
                        return $lastPayment ? $lastPayment->created_at->format('d/m/Y') : 'Belum ada';
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'suspended' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pelanggan')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
                SelectFilter::make('payment_year')
                    ->label('Tahun Pembayaran')
                    ->options([
                        '2023' => '2023',
                        '2024' => '2024',
                        '2025' => '2025',
                        '2026' => '2026',
                    ])
                    ->default(now()->year)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn(Builder $query, $year): Builder => $query->whereHas(
                                'payments',
                                fn(Builder $q) => $q->whereYear('payments.created_at', $year)
                            )
                        );
                    }),

                SelectFilter::make('payment_month')
                    ->label('Bulan Pembayaran')
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
                    ->default(now()->month)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn(Builder $query, $month): Builder => $query->whereHas(
                                'payments',
                                fn(Builder $q) => $q->whereMonth('payments.created_at', $month)
                            )
                        );
                    }),

                Filter::make('has_payment_selected_period')
                    ->label('Sudah Bayar Periode Terpilih')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereHas(
                            'payments',
                            fn(Builder $q) =>
                            $q->whereMonth('payments.created_at', request()->get('tableFilters.payment_month.value', now()->month))
                                ->whereYear('payments.created_at', request()->get('tableFilters.payment_year.value', now()->year))
                        )
                    ),
                Filter::make('no_payment_selected_period')
                    ->label('Belum Bayar Periode Terpilih')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereDoesntHave(
                            'payments',
                            fn(Builder $q) =>
                            $q->whereMonth('payments.created_at', request()->get('tableFilters.payment_month.value', now()->month))
                                ->whereYear('payments.created_at', request()->get('tableFilters.payment_year.value', now()->year))
                        )
                    ),
                Filter::make('payment_date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereHas(
                                    'payments',
                                    fn(Builder $q) => $q->whereDate('payments.created_at', '>=', $date)
                                ),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereHas(
                                    'payments',
                                    fn(Builder $q) => $q->whereDate('payments.created_at', '<=', $date)
                                ),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('customers.created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerReports::route('/'),
            'view' => Pages\ViewCustomerReport::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    public static function canViewAny(): bool
    {
        return true;
    }
}
