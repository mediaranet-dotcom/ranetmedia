<?php

namespace App\Filament\Resources\AutoInvoiceResource\Pages;

use App\Filament\Resources\AutoInvoiceResource;
use App\Services\InvoiceService;
use App\Services\EmailService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListAutoInvoices extends ListRecords
{
    protected static string $resource = AutoInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_monthly')
                ->label('Generate Invoice Bulanan')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Grid::make(2)
                        ->schema([
                            \Filament\Forms\Components\Select::make('month')
                                ->label('Bulan')
                                ->options([
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                ])
                                ->default(now()->month)
                                ->required(),
                            \Filament\Forms\Components\TextInput::make('year')
                                ->label('Tahun')
                                ->numeric()
                                ->default(now()->year)
                                ->required(),
                        ]),
                    \Filament\Forms\Components\Toggle::make('send_email')
                        ->label('Kirim Email Otomatis')
                        ->default(true)
                        ->helperText('Kirim invoice via email setelah generate'),
                    \Filament\Forms\Components\Toggle::make('dry_run')
                        ->label('Preview Mode (Dry Run)')
                        ->default(false)
                        ->helperText('Lihat preview tanpa membuat invoice'),
                ])
                ->action(function (array $data) {
                    try {
                        $invoiceService = app(InvoiceService::class);
                        $emailService = app(EmailService::class);
                        
                        if ($data['dry_run']) {
                            // Show preview
                            $this->showPreview($data['month'], $data['year']);
                            return;
                        }
                        
                        // Generate invoices
                        $invoices = $invoiceService->generateMonthlyInvoices($data['month'], $data['year']);
                        
                        if ($invoices->count() === 0) {
                            Notification::make()
                                ->title('Tidak Ada Invoice yang Dibuat')
                                ->body('Semua layanan sudah memiliki invoice untuk periode ini')
                                ->warning()
                                ->send();
                            return;
                        }
                        
                        $sent = 0;
                        $failed = 0;
                        
                        // Send emails if requested
                        if ($data['send_email']) {
                            foreach ($invoices as $invoice) {
                                if ($invoice->customer->email) {
                                    try {
                                        $emailService->sendInvoiceEmail($invoice);
                                        $sent++;
                                    } catch (\Exception $e) {
                                        $failed++;
                                    }
                                }
                            }
                        }
                        
                        $monthName = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ][$data['month']];
                        
                        $message = "Berhasil generate {$invoices->count()} invoice untuk {$monthName} {$data['year']}";
                        if ($data['send_email']) {
                            $message .= ". Email terkirim: {$sent}, Gagal: {$failed}";
                        }
                        
                        Notification::make()
                            ->title('Invoice Berhasil Dibuat')
                            ->body($message)
                            ->success()
                            ->send();
                            
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Generate Invoice')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
                
            Actions\Action::make('generate_due')
                ->label('Generate Invoice Jatuh Tempo')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Toggle::make('send_email')
                        ->label('Kirim Email Otomatis')
                        ->default(true),
                    \Filament\Forms\Components\Toggle::make('dry_run')
                        ->label('Preview Mode (Dry Run)')
                        ->default(false),
                ])
                ->action(function (array $data) {
                    try {
                        $invoiceService = app(InvoiceService::class);
                        $emailService = app(EmailService::class);
                        
                        if ($data['dry_run']) {
                            $this->showDuePreview();
                            return;
                        }
                        
                        $invoices = $invoiceService->generateDueInvoices();
                        
                        if ($invoices->count() === 0) {
                            Notification::make()
                                ->title('Tidak Ada Invoice yang Dibuat')
                                ->body('Tidak ada layanan yang jatuh tempo untuk billing')
                                ->warning()
                                ->send();
                            return;
                        }
                        
                        $sent = 0;
                        $failed = 0;
                        
                        if ($data['send_email']) {
                            foreach ($invoices as $invoice) {
                                if ($invoice->customer->email) {
                                    try {
                                        $emailService->sendInvoiceEmail($invoice);
                                        $sent++;
                                    } catch (\Exception $e) {
                                        $failed++;
                                    }
                                }
                            }
                        }
                        
                        $message = "Berhasil generate {$invoices->count()} invoice untuk layanan yang jatuh tempo";
                        if ($data['send_email']) {
                            $message .= ". Email terkirim: {$sent}, Gagal: {$failed}";
                        }
                        
                        Notification::make()
                            ->title('Invoice Berhasil Dibuat')
                            ->body($message)
                            ->success()
                            ->send();
                            
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Generate Invoice')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
                
            Actions\Action::make('send_overdue_reminders')
                ->label('Kirim Pengingat Terlambat')
                ->icon('heroicon-o-bell-alert')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Kirim Pengingat Pembayaran Terlambat')
                ->modalDescription('Kirim email pengingat ke semua customer yang memiliki tagihan terlambat?')
                ->action(function () {
                    try {
                        $emailService = app(EmailService::class);
                        $results = $emailService->sendOverdueReminders();
                        
                        Notification::make()
                            ->title('Pengingat Terlambat Selesai')
                            ->body("Terkirim: {$results['sent']}, Gagal: {$results['failed']}, Dilewati: {$results['skipped']}")
                            ->success()
                            ->send();
                            
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Kirim Pengingat')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
    
    private function showPreview(int $month, int $year): void
    {
        // This would show a preview modal - simplified for now
        Notification::make()
            ->title('Preview Mode')
            ->body('Fitur preview akan menampilkan daftar layanan yang akan dibuatkan invoice')
            ->info()
            ->send();
    }
    
    private function showDuePreview(): void
    {
        // This would show a preview modal - simplified for now
        Notification::make()
            ->title('Preview Mode')
            ->body('Fitur preview akan menampilkan daftar layanan yang jatuh tempo untuk billing')
            ->info()
            ->send();
    }
}
