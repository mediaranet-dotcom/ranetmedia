<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Invoice;
use Carbon\Carbon;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    protected static string $view = 'filament.pages.dashboard';

    protected static ?int $navigationSort = -2;

    public function getWidgets(): array
    {
        return [
            // Widget chart dihapus karena masalah styling di light mode
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\SimpleDashboardStats::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Widget footer dihapus karena masalah styling di light mode
        ];
    }

    public function getPaymentStats()
    {
        // Menggunakan status 'completed' sesuai dengan data yang ada
        $totalPaid = Payment::where('status', 'completed')->sum('amount');
        $totalCount = Payment::where('status', 'completed')->count();

        $thisMonth = Payment::where('status', 'completed')
            ->whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');

        $thisMonthCount = Payment::where('status', 'completed')
            ->whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->count();

        // Untuk pending, kita bisa menggunakan status lain atau membuat logic berbeda
        // Sementara kita set 0 karena semua pembayaran sudah completed
        $pendingCount = 0;
        $pendingAmount = 0;

        return [
            'total_paid' => $totalPaid,
            'total_count' => $totalCount,
            'this_month' => $thisMonth,
            'this_month_count' => $thisMonthCount,
            'this_month_name' => Carbon::now()->locale('id')->isoFormat('MMMM'),
            'pending_count' => $pendingCount,
            'pending_amount' => $pendingAmount,
        ];
    }

    public function getDashboardStats()
    {
        // Total customers
        $totalCustomers = Customer::count();

        // Customers with payments this month
        $paidThisMonth = Customer::whereHas('payments', function ($query) {
            $query->whereMonth('payments.created_at', now()->month)
                ->whereYear('payments.created_at', now()->year);
        })->count();

        // Total payment amount this month
        $totalPaymentThisMonth = Payment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Unpaid customers this month
        $unpaidThisMonth = $totalCustomers - $paidThisMonth;

        // Outstanding invoices
        $outstandingInvoices = Invoice::whereIn('status', ['sent', 'partial_paid', 'overdue'])->count();
        $outstandingAmount = Invoice::whereIn('status', ['sent', 'partial_paid', 'overdue'])->sum('outstanding_amount');

        return [
            'total_customers' => $totalCustomers,
            'paid_this_month' => $paidThisMonth,
            'unpaid_this_month' => $unpaidThisMonth,
            'total_payment_this_month' => $totalPaymentThisMonth,
            'outstanding_invoices' => $outstandingInvoices,
            'outstanding_amount' => $outstandingAmount,
            'month_name' => Carbon::now()->locale('id')->isoFormat('MMMM YYYY'),
        ];
    }

    public function getRecentPayments()
    {
        return Payment::with(['service.customer', 'invoice.customer'])
            ->where('status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();
    }
}
