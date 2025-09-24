<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Package;
use Carbon\Carbon;

class SystemInfoWidget extends Widget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini

    protected static string $view = 'filament.widgets.system-info-widget';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now();

        return [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('status', 'active')->count(),
            'total_packages' => Package::where('is_active', true)->count(),
            'payments_today' => Payment::whereDate('payment_date', $today)->count(),
            'payments_this_month' => Payment::whereMonth('payment_date', $thisMonth->month)
                ->whereYear('payment_date', $thisMonth->year)
                ->count(),
            'revenue_today' => Payment::whereDate('payment_date', $today)
                ->where('status', 'completed')
                ->sum('amount'),
            'revenue_this_month' => Payment::whereMonth('payment_date', $thisMonth->month)
                ->whereYear('payment_date', $thisMonth->year)
                ->where('status', 'completed')
                ->sum('amount'),
            'pending_invoices' => Invoice::whereIn('status', ['sent', 'partial_paid'])->count(),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
            'current_date' => $today->locale('id')->isoFormat('dddd, DD MMMM YYYY'),
            'current_month' => $thisMonth->locale('id')->isoFormat('MMMM YYYY'),
        ];
    }
}
