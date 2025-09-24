<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationGroup = 'Billing';

    protected static ?string $navigationLabel = 'Pembayaran';

    protected static ?string $modelLabel = 'Pembayaran';

    protected static ?string $pluralModelLabel = 'Pembayaran';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pembayaran')
                    ->description('Pilih invoice yang belum lunas untuk diproses pembayarannya')
                    ->schema([
                        Forms\Components\Select::make('invoice_id')
                            ->relationship(
                                'invoice',
                                'invoice_number',
                                fn($query) => $query->where('status', '!=', 'paid')
                                    ->where('outstanding_amount', '>', 0)
                                    ->with(['customer'])
                            )
                            ->getOptionLabelFromRecordUsing(function (\App\Models\Invoice $record) {
                                $statusLabels = [
                                    'draft' => 'Draft',
                                    'sent' => 'Terkirim',
                                    'partial_paid' => 'Sebagian Lunas',
                                    'overdue' => 'Terlambat',
                                ];
                                $statusLabel = $statusLabels[$record->status] ?? $record->status;

                                // Tambahkan informasi periode bulan dari invoice
                                $periodInfo = '';
                                if ($record->billing_period_start && $record->billing_period_end) {
                                    $monthNames = [
                                        1 => 'Jan',
                                        2 => 'Feb',
                                        3 => 'Mar',
                                        4 => 'Apr',
                                        5 => 'Mei',
                                        6 => 'Jun',
                                        7 => 'Jul',
                                        8 => 'Ags',
                                        9 => 'Sep',
                                        10 => 'Okt',
                                        11 => 'Nov',
                                        12 => 'Des'
                                    ];

                                    $startMonth = $monthNames[$record->billing_period_start->month] ?? $record->billing_period_start->month;
                                    $year = $record->billing_period_start->year;
                                    $periodInfo = " - Periode: {$startMonth} {$year}";
                                }

                                return "{$record->invoice_number} - {$record->customer->name}{$periodInfo} [{$statusLabel}] (Sisa: Rp " . number_format($record->outstanding_amount, 0, ',', '.') . ")";
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->placeholder('Pilih invoice yang belum lunas')
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $invoice = \App\Models\Invoice::find($state);
                                    if ($invoice) {
                                        $set('service_id', $invoice->service_id);
                                        $set('amount', $invoice->outstanding_amount);

                                        // Set year dan month dari billing period invoice
                                        if ($invoice->billing_period_start) {
                                            $set('year', $invoice->billing_period_start->year);
                                            $set('month', $invoice->billing_period_start->month);
                                        }
                                    }
                                }
                            }),
                        Forms\Components\Select::make('service_id')
                            ->relationship('service', 'id')
                            ->getOptionLabelFromRecordUsing(fn(\App\Models\Service $record) => "{$record->customer->name} - {$record->package->name}")
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated()
                            ->reactive(),
                        // Field tahun (otomatis dari invoice)
                        Forms\Components\Select::make('year')
                            ->options(function () {
                                $currentYear = now()->year;
                                $years = range($currentYear - 5, $currentYear + 5); // Rentang tahun
                                return array_combine($years, $years);
                            })
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->helperText('Tahun akan otomatis terisi dari periode invoice yang dipilih'),

                        // Field bulan (otomatis dari invoice)
                        Forms\Components\Select::make('month')
                            ->options([
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
                                12 => 'Desember',
                            ])
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->helperText('Bulan akan otomatis terisi dari periode invoice yang dipilih'),

                        // Komponen untuk menampilkan peringatan pembayaran existing
                        Forms\Components\Placeholder::make('payment_warning')
                            ->label('')
                            ->content(function ($get, $record) {
                                $serviceId = $get('service_id');
                                $month = $get('month');
                                $year = $get('year');

                                if (!$serviceId || !$month || !$year) {
                                    return '';
                                }

                                $existingPayment = Payment::getPaymentForPeriod($serviceId, $month, $year);

                                if ($existingPayment && (!$record || $existingPayment->id !== $record->id)) {
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

                                    $periodeName = $monthNames[$month] . ' ' . $year;
                                    $amount = number_format($existingPayment->amount, 0, ',', '.');

                                    return "⚠️ PERINGATAN: Sudah ada pembayaran untuk periode {$periodeName} sebesar Rp {$amount} pada tanggal {$existingPayment->payment_date->format('d/m/Y')}. Tidak dapat membuat pembayaran ganda dalam periode yang sama.";
                                }

                                return '';
                            })
                            ->visible(function ($get, $record) {
                                $serviceId = $get('service_id');
                                $month = $get('month');
                                $year = $get('year');

                                if (!$serviceId || !$month || !$year) {
                                    return false;
                                }

                                $existingPayment = Payment::getPaymentForPeriod($serviceId, $month, $year);
                                return $existingPayment && (!$record || $existingPayment->id !== $record->id);
                            })
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Pembayaran')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->helperText('Jumlah akan otomatis terisi sesuai sisa tagihan invoice'),
                        Forms\Components\DatePicker::make('payment_date')
                            ->required(),
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'bank_transfer' => 'Bank Transfer',
                                'e_wallet' => 'E-Wallet',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('completed')
                            ->required(),
                        Forms\Components\TextInput::make('reference_number'),
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('Transaction ID'),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('payment_notes')
                            ->label('Payment Notes')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('No. Invoice')
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('invoice', function ($q) use ($search) {
                            $q->where('invoice_number', 'like', "%{$search}%");
                        });
                    })
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('service.customer.customer_number')
                    ->label('No. Pelanggan')
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('service.customer', function ($q) use ($search) {
                            $q->where('customer_number', 'like', "%{$search}%");
                        });
                    })
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('service.customer.name')
                    ->label('Nama Pelanggan')
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('service.customer', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.package.name')
                    ->label('Paket')
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('service.package', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total Pembayaran'),
                        Tables\Columns\Summarizers\Count::make()
                            ->label('Jumlah Transaksi')
                    ]),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->badge()
                    ->color('info')
                    ->sortable(false)
                    ->getStateUsing(function ($record) {
                        if ($record->invoice && $record->invoice->billing_period_start) {
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

                            $month = $monthNames[$record->invoice->billing_period_start->month] ?? $record->invoice->billing_period_start->month;
                            $year = $record->invoice->billing_period_start->year;
                            return "{$month}-{$year}";
                        }

                        // Fallback ke periode dari payment
                        if ($record->month && $record->year) {
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

                            $month = $monthNames[$record->month] ?? $record->month;
                            return "{$month}-{$record->year}";
                        }

                        return '-';
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(function ($state) {
                        switch ($state) {
                            case 'completed':
                                return 'success';
                            case 'pending':
                                return 'warning';
                            case 'failed':
                                return 'danger';
                            case 'cancelled':
                                return 'gray';
                            default:
                                return 'gray';
                        }
                    }),
                Tables\Columns\TextColumn::make('payment_method')
                    ->formatStateUsing(function (string $state): string {
                        switch ($state) {
                            case 'cash':
                                return 'Cash';
                            case 'bank_transfer':
                                return 'Bank Transfer';
                            case 'e_wallet':
                                return 'E-Wallet';
                            case 'other':
                                return 'Other';
                            default:
                                return $state;
                        }
                    }),
                Tables\Columns\TextColumn::make('reference_number'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'bank_transfer' => 'Bank Transfer',
                        'e_wallet' => 'E-Wallet',
                        'other' => 'Other',
                    ]),
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100, 'all'])
            ->defaultSort('payment_date', 'desc');
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
