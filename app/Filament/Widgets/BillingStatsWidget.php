<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Invoice;
use App\Services\InvoiceService;

class BillingStatsWidget extends BaseWidget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $invoiceService = new InvoiceService();

        // Total Outstanding
        $totalOutstanding = Invoice::whereIn('status', ['sent', 'partial_paid', 'overdue'])
            ->sum('outstanding_amount');

        // Monthly Revenue (paid this month)
        $monthlyRevenue = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_amount');

        // Overdue Count
        $overdueCount = Invoice::where('due_date', '<', now())
            ->whereIn('status', ['sent', 'partial_paid'])
            ->count();

        // This Month Invoices
        $thisMonthInvoices = Invoice::whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->count();

        // Collection Rate (percentage of invoices paid on time)
        $totalInvoicesThisMonth = Invoice::whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->count();

        $paidOnTimeThisMonth = Invoice::whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->where('status', 'paid')
            ->where('paid_at', '<=', \DB::raw('due_date'))
            ->count();

        $collectionRate = $totalInvoicesThisMonth > 0
            ? round(($paidOnTimeThisMonth / $totalInvoicesThisMonth) * 100, 1)
            : 0;

        return [
            Stat::make('Total Piutang', 'Rp ' . number_format($totalOutstanding, 0, ',', '.'))
                ->description('Outstanding invoices')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($totalOutstanding > 0 ? 'warning' : 'success'),

            Stat::make('Revenue Bulan Ini', 'Rp ' . number_format($monthlyRevenue, 0, ',', '.'))
                ->description('Pembayaran diterima')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Invoice Terlambat', $overdueCount)
                ->description('Melewati jatuh tempo')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueCount > 0 ? 'danger' : 'success'),

            Stat::make('Invoice Bulan Ini', $thisMonthInvoices)
                ->description('Total invoice dibuat')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Collection Rate', $collectionRate . '%')
                ->description('Tingkat pembayaran tepat waktu')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($collectionRate >= 80 ? 'success' : ($collectionRate >= 60 ? 'warning' : 'danger')),
        ];
    }
}
