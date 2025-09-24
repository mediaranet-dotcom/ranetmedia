<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Payment;

class SimpleRecentPayments extends Widget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini

    protected static string $view = 'filament.widgets.simple-recent-payments';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        try {
            $recentPayments = Payment::with(['service.customer', 'invoice.customer'])
                ->where('status', 'completed')
                ->orderBy('payment_date', 'desc')
                ->limit(5)
                ->get();

            return [
                'payments' => $recentPayments
            ];
        } catch (\Exception) {
            return [
                'payments' => collect([])
            ];
        }
    }
}
