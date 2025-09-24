<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class AutoInvoiceStatsWidget extends BaseWidget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static ?string $pollingInterval = '30s';

    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        // Invoice statistics
        $totalInvoices = Invoice::count();
        $thisMonthInvoices = Invoice::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Overdue invoices
        $overdueInvoices = Invoice::where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();

        $overdueAmount = Invoice::where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->sum('outstanding_amount');

        // Auto billing enabled services
        $autoBillingServices = Service::where('status', 'active')
            ->where('auto_billing', true)
            ->count();

        // Services due for billing
        $dueForBilling = Service::where('status', 'active')
            ->where('auto_billing', true)
            ->where(function ($query) {
                $query->whereNull('next_billing_date')
                    ->orWhere('next_billing_date', '<=', now());
            })
            ->count();

        // Pending invoices (sent but not paid)
        $pendingInvoices = Invoice::whereIn('status', ['sent', 'partial_paid'])
            ->count();

        $pendingAmount = Invoice::whereIn('status', ['sent', 'partial_paid'])
            ->sum('outstanding_amount');

        return [
            Stat::make('Invoice Bulan Ini', $thisMonthInvoices)
                ->description("dari {$totalInvoices} total invoice")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Invoice Terlambat', $overdueInvoices)
                ->description('Rp ' . number_format($overdueAmount, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueInvoices > 0 ? 'danger' : 'success'),

            Stat::make('Layanan Auto Billing', $autoBillingServices)
                ->description('Layanan dengan billing otomatis')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('info'),

            Stat::make('Siap untuk Billing', $dueForBilling)
                ->description('Layanan yang siap dibuatkan invoice')
                ->descriptionIcon('heroicon-m-clock')
                ->color($dueForBilling > 0 ? 'warning' : 'success'),

            Stat::make('Invoice Pending', $pendingInvoices)
                ->description('Rp ' . number_format($pendingAmount, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($pendingInvoices > 0 ? 'warning' : 'success'),
        ];
    }
}
