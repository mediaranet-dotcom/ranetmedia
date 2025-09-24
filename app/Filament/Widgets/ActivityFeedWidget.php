<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Invoice;

class ActivityFeedWidget extends Widget
{
    protected static string $view = 'filament.widgets.activity-feed-widget';

    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $activities = collect();

        // Recent customers (last 5)
        $recentCustomers = Customer::latest()->limit(5)->get();
        foreach ($recentCustomers as $customer) {
            $activities->push([
                'type' => 'customer',
                'icon' => 'user-plus',
                'color' => 'blue',
                'title' => 'Pelanggan Baru',
                'description' => $customer->name . ' telah terdaftar',
                'time' => $customer->created_at,
                'data' => $customer
            ]);
        }

        // Recent payments (last 5)
        try {
            $recentPayments = Payment::with(['service.customer', 'invoice.customer'])->latest('payment_date')->limit(5)->get();
            foreach ($recentPayments as $payment) {
                $customerName = 'N/A';

                // Try to get customer name safely
                if ($payment->service && $payment->service->customer) {
                    $customerName = $payment->service->customer->name;
                } elseif ($payment->invoice && $payment->invoice->customer) {
                    $customerName = $payment->invoice->customer->name;
                }

                $activities->push([
                    'type' => 'payment',
                    'icon' => 'banknotes',
                    'color' => 'green',
                    'title' => 'Pembayaran Diterima',
                    'description' => $customerName . ' - Rp ' . number_format($payment->amount, 0, ',', '.'),
                    'time' => $payment->payment_date,
                    'data' => $payment
                ]);
            }
        } catch (\Exception) {
            // Skip payments if there's an error
        }

        // Recent invoices (last 5)
        $recentInvoices = Invoice::with('customer')->latest()->limit(5)->get();
        foreach ($recentInvoices as $invoice) {
            $activities->push([
                'type' => 'invoice',
                'icon' => 'document-text',
                'color' => 'orange',
                'title' => 'Invoice Dibuat',
                'description' => 'Invoice untuk ' . ($invoice->customer->name ?? 'N/A'),
                'time' => $invoice->created_at,
                'data' => $invoice
            ]);
        }

        // Sort by time (newest first) and take 10
        $activities = $activities->sortByDesc('time')->take(10)->values();

        return [
            'activities' => $activities
        ];
    }
}
