<?php

namespace App\Filament\Pages;

use App\Models\Payment;
use App\Exports\PaymentPeriodReportExport;
use Filament\Pages\Page;
use Filament\Actions;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class PaymentPeriodReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.pages.payment-period-report';

    protected static ?string $navigationLabel = 'Laporan Periode';

    protected static ?string $title = 'Laporan Pembayaran Per Periode';

    protected static ?int $navigationSort = 10;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_excel')
                ->label('Export ke Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    return $this->exportToExcel();
                }),
            Actions\Action::make('export_current_year')
                ->label('Export Tahun Ini')
                ->icon('heroicon-o-calendar')
                ->color('info')
                ->action(function () {
                    return $this->exportToExcel(now()->year);
                }),
        ];
    }

    public function exportToExcel($year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $filename = 'laporan-pembayaran-periode-' . $year;

        if ($month) {
            $monthName = Carbon::create($year, $month, 1)->format('m');
            $filename .= '-' . $monthName;
        }

        $filename .= '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new PaymentPeriodReportExport($year, $month), $filename);
    }

    public function getPaymentsByPeriod()
    {
        $payments = Payment::with(['service.customer', 'invoice.customer'])
            ->where('status', 'completed')
            ->whereNotNull('payment_date')
            ->orderBy('payment_date', 'desc')
            ->get();

        $groupedPayments = [];

        foreach ($payments as $payment) {
            $period = Carbon::parse($payment->payment_date)->locale('id')->isoFormat('MMMM YYYY');

            if (!isset($groupedPayments[$period])) {
                $groupedPayments[$period] = [
                    'period' => $period,
                    'payments' => [],
                    'total' => 0,
                    'count' => 0
                ];
            }

            $groupedPayments[$period]['payments'][] = $payment;
            $groupedPayments[$period]['total'] += $payment->amount;
            $groupedPayments[$period]['count']++;
        }

        return $groupedPayments;
    }
}
