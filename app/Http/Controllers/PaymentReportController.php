<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentReportController extends Controller
{
    public function print(Request $request)
    {
        $query = Payment::with(['invoice.customer'])
            ->orderBy('payment_date', 'desc');

        // Apply filters from request
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        $payments = $query->get();
        
        // Calculate summary
        $totalPayments = $payments->count();
        $totalAmount = $payments->sum('amount');
        $onTimePayments = $payments->filter(function ($payment) {
            return $payment->invoice && $payment->payment_date <= $payment->invoice->due_date;
        })->count();
        $latePayments = $totalPayments - $onTimePayments;

        $data = [
            'payments' => $payments,
            'totalPayments' => $totalPayments,
            'totalAmount' => $totalAmount,
            'onTimePayments' => $onTimePayments,
            'latePayments' => $latePayments,
            'printDate' => now(),
            'filters' => $request->all()
        ];

        $pdf = Pdf::loadView('reports.payment-report', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->stream('laporan-pembayaran-' . now()->format('Y-m-d') . '.pdf');
    }
}
