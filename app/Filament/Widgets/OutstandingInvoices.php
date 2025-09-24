<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\Widget;

class OutstandingInvoices extends Widget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static string $view = 'filament.widgets.outstanding-invoices';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 5;

    public function getViewData(): array
    {
        $outstandingInvoices = Invoice::with(['customer'])
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();

        $totalOutstanding = Invoice::where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->sum('total_amount');

        return [
            'invoices' => $outstandingInvoices,
            'totalOutstanding' => $totalOutstanding,
            'count' => $outstandingInvoices->count()
        ];
    }
}
