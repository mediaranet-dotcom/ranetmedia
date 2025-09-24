<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Services\WhatsAppService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Invoice';

    protected static ?string $modelLabel = 'Invoice';

    protected static ?string $pluralModelLabel = 'Invoice';

    protected static ?string $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Invoice')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Nomor Invoice')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated'),
                        Forms\Components\Select::make('customer_id')
                            ->label('Pelanggan')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Reset service when customer changes
                                $set('service_id', null);
                                $set('subtotal', 0);
                                $set('tax_amount', 0);
                                $set('total_amount', 0);
                                $set('outstanding_amount', 0);
                            }),
                        Forms\Components\Select::make('service_id')
                            ->label('Layanan')
                            ->options(function (callable $get) {
                                $customerId = $get('customer_id');
                                if (!$customerId) {
                                    return [];
                                }

                                return \App\Models\Service::where('customer_id', $customerId)
                                    ->with('package')
                                    ->get()
                                    ->mapWithKeys(function ($service) {
                                        $price = $service->monthly_fee ?? $service->package->price ?? 0;
                                        return [$service->id => "{$service->package->name} - Rp " . number_format($price, 0, ',', '.')];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->placeholder('Pilih pelanggan terlebih dahulu')
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $service = \App\Models\Service::with('package')->find($state);
                                    if ($service) {
                                        $monthlyFee = $service->monthly_fee ?? $service->package->price ?? 0;
                                        $set('subtotal', $monthlyFee);

                                        // Recalculate totals
                                        $taxRate = $get('tax_rate') ?? 0;
                                        $discount = $get('discount_amount') ?? 0;
                                        $taxAmount = $monthlyFee * $taxRate;
                                        $total = $monthlyFee + $taxAmount - $discount;

                                        $set('tax_amount', $taxAmount);
                                        $set('total_amount', $total);
                                        $set('outstanding_amount', $total - ($get('paid_amount') ?? 0));
                                    }
                                } else {
                                    // Reset values when no service selected
                                    $set('subtotal', 0);
                                    $set('tax_amount', 0);
                                    $set('total_amount', 0);
                                    $set('outstanding_amount', 0);
                                }
                            }),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Terkirim',
                                'paid' => 'Lunas',
                                'partial_paid' => 'Sebagian Lunas',
                                'overdue' => 'Terlambat',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required()
                            ->default('draft'),
                    ])->columns(2),

                Forms\Components\Section::make('Periode Tagihan')
                    ->schema([
                        Forms\Components\DatePicker::make('invoice_date')
                            ->label('Tanggal Invoice')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Jatuh Tempo')
                            ->required(),
                        Forms\Components\DatePicker::make('billing_period_start')
                            ->label('Periode Mulai')
                            ->required(),
                        Forms\Components\DatePicker::make('billing_period_end')
                            ->label('Periode Selesai')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Rincian Biaya')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal (Otomatis dari Layanan)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $taxRate = $get('tax_rate') ?? 0;
                                $discount = $get('discount_amount') ?? 0;
                                $taxAmount = ($state ?? 0) * $taxRate;
                                $total = ($state ?? 0) + $taxAmount - $discount;

                                $set('tax_amount', $taxAmount);
                                $set('total_amount', $total);
                                $set('outstanding_amount', $total - ($get('paid_amount') ?? 0));
                            }),
                        Forms\Components\Select::make('tax_rate')
                            ->label('Pajak (PPN)')
                            ->options([
                                0 => 'Tanpa PPN (0%)',
                                0.11 => 'PPN 11%',
                                0.12 => 'PPN 12%',
                            ])
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $subtotal = $get('subtotal') ?? 0;
                                $discount = $get('discount_amount') ?? 0;
                                $taxAmount = $subtotal * $state;
                                $total = $subtotal + $taxAmount - $discount;

                                $set('tax_amount', $taxAmount);
                                $set('total_amount', $total);
                                $set('outstanding_amount', $total - ($get('paid_amount') ?? 0));
                            }),
                        Forms\Components\TextInput::make('tax_amount')
                            ->label('Jumlah Pajak (Otomatis Dihitung)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Diskon')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $subtotal = $get('subtotal') ?? 0;
                                $taxAmount = $get('tax_amount') ?? 0;
                                $total = $subtotal + $taxAmount - $state;

                                $set('total_amount', $total);
                                $set('outstanding_amount', $total - ($get('paid_amount') ?? 0));
                            }),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total (Otomatis Dihitung)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('paid_amount')
                            ->label('Sudah Dibayar')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $totalAmount = $get('total_amount') ?? 0;
                                $paidAmount = $state ?? 0;
                                $outstanding = $totalAmount - $paidAmount;
                                $set('outstanding_amount', max(0, $outstanding));
                            }),
                        Forms\Components\TextInput::make('outstanding_amount')
                            ->label('Sisa Tagihan (Otomatis Dihitung)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(3),

                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('customer.customer_number')
                    ->label('No. Pelanggan')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.package.name')
                    ->label('Paket')
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice_date')
                    ->label('Tgl Invoice')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn($record) => $record->isOverdue() ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('tax_rate')
                    ->label('PPN')
                    ->formatStateUsing(fn($state) => $state > 0 ? number_format($state * 100, 0) . '%' : 'Tanpa PPN')
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('outstanding_amount')
                    ->label('Sisa')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn($state) => $state > 0 ? 'warning' : 'success'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(function ($state) {
                        switch ($state) {
                            case 'draft':
                                return 'gray';
                            case 'sent':
                                return 'info';
                            case 'paid':
                                return 'success';
                            case 'partial_paid':
                                return 'warning';
                            case 'overdue':
                                return 'danger';
                            case 'cancelled':
                                return 'secondary';
                            default:
                                return 'gray';
                        }
                    })
                    ->formatStateUsing(function ($state) {
                        switch ($state) {
                            case 'draft':
                                return 'Draft';
                            case 'sent':
                                return 'Terkirim';
                            case 'paid':
                                return 'Lunas';
                            case 'partial_paid':
                                return 'Sebagian';
                            case 'overdue':
                                return 'Terlambat';
                            case 'cancelled':
                                return 'Dibatalkan';
                            default:
                                return ucfirst($state);
                        }
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Terkirim',
                        'paid' => 'Lunas',
                        'partial_paid' => 'Sebagian Lunas',
                        'overdue' => 'Terlambat',
                        'cancelled' => 'Dibatalkan',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->label('Terlambat')
                    ->query(fn($query) => $query->where('due_date', '<', now())
                        ->whereIn('status', ['sent', 'partial_paid']))
                    ->toggle(),
                Tables\Filters\Filter::make('this_month')
                    ->label('Bulan Ini')
                    ->query(fn($query) => $query->whereMonth('invoice_date', now()->month)
                        ->whereYear('invoice_date', now()->year))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn(Invoice $record) => route('invoice.download', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('view_pdf')
                    ->label('Lihat PDF')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn(Invoice $record) => route('invoice.pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('send_whatsapp')
                    ->label('Kirim WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->visible(fn(Invoice $record) => $record->customer->phone)
                    ->action(function (Invoice $record) {
                        $whatsappService = app(WhatsAppService::class);
                        $result = $whatsappService->sendInvoiceNotification($record);

                        if ($result) {
                            if (env('WHATSAPP_TEST_MODE', false)) {
                                $testMessages = \Cache::get('whatsapp_test_messages', []);
                                $latestMessage = end($testMessages);

                                Notification::make()
                                    ->title('WhatsApp Test Mode')
                                    ->body('Pesan berhasil dibuat. Klik URL untuk mengirim manual.')
                                    ->success()
                                    ->actions([
                                        \Filament\Notifications\Actions\Action::make('open_whatsapp')
                                            ->label('Buka WhatsApp')
                                            ->url($latestMessage['url'] ?? '#')
                                            ->openUrlInNewTab()
                                    ])
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('WhatsApp Terkirim')
                                    ->body("Invoice berhasil dikirim ke {$record->customer->name}")
                                    ->success()
                                    ->send();
                            }
                        } else {
                            Notification::make()
                                ->title('WhatsApp Gagal')
                                ->body('Gagal mengirim pesan WhatsApp. Periksa log untuk detail.')
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Invoice via WhatsApp')
                    ->modalDescription(fn(Invoice $record) => "Kirim invoice {$record->invoice_number} ke {$record->customer->name} ({$record->customer->phone})?"),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn(Invoice $record) => in_array($record->status, ['draft', 'cancelled']))
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Invoice')
                    ->modalDescription(fn(Invoice $record) => "Yakin ingin menghapus invoice {$record->invoice_number}? Aksi ini tidak dapat dibatalkan.")
                    ->successNotificationTitle('Invoice berhasil dihapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export_pdf')
                        ->label('Export PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $invoiceService = new InvoiceService();

                            if ($records->count() === 1) {
                                // Single invoice - redirect to download route
                                return redirect()->route('invoice.download', $records->first());
                            }

                            // Multiple invoices - create ZIP
                            $zip = new \ZipArchive();
                            $zipFileName = 'invoices-' . now()->format('Y-m-d-H-i-s') . '.zip';
                            $zipPath = storage_path('app/temp/' . $zipFileName);

                            // Create temp directory if not exists
                            if (!file_exists(dirname($zipPath))) {
                                mkdir(dirname($zipPath), 0755, true);
                            }

                            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                                foreach ($records as $invoice) {
                                    $pdf = $invoiceService->generatePDF($invoice);
                                    $pdfContent = $pdf->output();
                                    $zip->addFromString("invoice-{$invoice->invoice_number}.pdf", $pdfContent);
                                }
                                $zip->close();

                                return response()->download($zipPath, $zipFileName)->deleteFileAfterSend();
                            }

                            throw new \Exception('Could not create ZIP file');
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Export Invoices to PDF')
                        ->modalDescription('This will download all selected invoices as PDF files.'),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
