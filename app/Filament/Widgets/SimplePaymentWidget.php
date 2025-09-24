<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Payment;
use App\Models\Invoice;

class SimplePaymentWidget extends Widget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static string $view = 'filament.widgets.simple-payment';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function getViewData(): array
    {
        // Safe data retrieval with fallbacks
        try {
            $totalPayments = Payment::sum('amount') ?? 0;
            $todayPayments = Payment::whereDate('payment_date', today())->sum('amount') ?? 0;
            $paymentCount = Payment::count() ?? 0;
            $todayCount = Payment::whereDate('payment_date', today())->count() ?? 0;

            $totalInvoices = Invoice::count() ?? 0;
            $paidInvoices = Invoice::where('status', 'paid')->count() ?? 0;
            $unpaidInvoices = $totalInvoices - $paidInvoices;

            $recentPayments = Payment::with('customer')
                ->orderBy('payment_date', 'desc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            // Fallback values if database error
            $totalPayments = 0;
            $todayPayments = 0;
            $paymentCount = 0;
            $todayCount = 0;
            $totalInvoices = 0;
            $paidInvoices = 0;
            $unpaidInvoices = 0;
            $recentPayments = collect();
        }

        return [
            'totalPayments' => $totalPayments,
            'todayPayments' => $todayPayments,
            'paymentCount' => $paymentCount,
            'todayCount' => $todayCount,
            'totalInvoices' => $totalInvoices,
            'paidInvoices' => $paidInvoices,
            'unpaidInvoices' => $unpaidInvoices,
            'recentPayments' => $recentPayments,
        ];
    }
}
