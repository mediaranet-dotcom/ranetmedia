<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PaymentPeriodReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $year;
    protected $month;
    protected $paymentsByPeriod;
    protected $totalRevenue;
    protected $totalTransactions;

    public function __construct($year = null, $month = null)
    {
        $this->year = $year ?? now()->year;
        $this->month = $month;
        $this->calculateData();
    }

    protected function calculateData()
    {
        // Get payments by period
        $query = Payment::with(['service.customer', 'service.package', 'invoice'])
            ->whereYear('payment_date', $this->year);

        if ($this->month) {
            $query->whereMonth('payment_date', $this->month);
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        // Group by period (month-year)
        $this->paymentsByPeriod = $payments->groupBy(function ($payment) {
            return $payment->payment_date->format('Y-m');
        })->map(function ($periodPayments, $period) {
            $periodDate = \Carbon\Carbon::createFromFormat('Y-m', $period);
            return [
                'period' => $periodDate->format('F Y'),
                'period_short' => $periodDate->format('M Y'),
                'month' => $periodDate->month,
                'year' => $periodDate->year,
                'payments' => $periodPayments,
                'count' => $periodPayments->count(),
                'total' => $periodPayments->sum('amount'),
                'customers' => $periodPayments->pluck('service.customer.name')->unique()->sort()->values(),
            ];
        })->sortByDesc('year')->sortByDesc('month');

        $this->totalRevenue = $this->paymentsByPeriod->sum('total');
        $this->totalTransactions = $this->paymentsByPeriod->sum('count');
    }

    public function collection()
    {
        $data = collect();

        // Add summary header
        $data->push([
            'type' => 'SUMMARY',
            'period' => 'RINGKASAN LAPORAN',
            'customer' => '',
            'package' => '',
            'amount' => '',
            'date' => '',
            'invoice' => '',
            'status' => ''
        ]);

        $data->push([
            'type' => 'SUMMARY',
            'period' => 'Total Periode',
            'customer' => $this->paymentsByPeriod->count() . ' periode',
            'package' => '',
            'amount' => $this->totalRevenue,
            'date' => '',
            'invoice' => '',
            'status' => ''
        ]);

        $data->push([
            'type' => 'SUMMARY',
            'period' => 'Total Transaksi',
            'customer' => $this->totalTransactions . ' transaksi',
            'package' => '',
            'amount' => '',
            'date' => '',
            'invoice' => '',
            'status' => ''
        ]);

        $data->push([
            'type' => 'SUMMARY',
            'period' => 'Rata-rata per Periode',
            'customer' => '',
            'package' => '',
            'amount' => $this->paymentsByPeriod->count() > 0 ? $this->totalRevenue / $this->paymentsByPeriod->count() : 0,
            'date' => '',
            'invoice' => '',
            'status' => ''
        ]);

        // Add empty row
        $data->push([
            'type' => 'EMPTY',
            'period' => '',
            'customer' => '',
            'package' => '',
            'amount' => '',
            'date' => '',
            'invoice' => '',
            'status' => ''
        ]);

        // Add detail data by period
        foreach ($this->paymentsByPeriod as $periodData) {
            // Period header
            $data->push([
                'type' => 'PERIOD_HEADER',
                'period' => $periodData['period'],
                'customer' => $periodData['count'] . ' transaksi',
                'package' => '',
                'amount' => $periodData['total'],
                'date' => '',
                'invoice' => '',
                'status' => ''
            ]);

            // Period payments
            foreach ($periodData['payments'] as $payment) {
                $data->push([
                    'type' => 'PAYMENT',
                    'period' => '',
                    'customer' => $payment->service->customer->name ?? 'N/A',
                    'package' => $payment->service->package->name ?? 'N/A',
                    'amount' => $payment->amount,
                    'date' => $payment->payment_date->format('d/m/Y'),
                    'invoice' => $payment->invoice->invoice_number ?? 'N/A',
                    'status' => $payment->status ?? 'completed'
                ]);
            }

            // Add empty row after each period
            $data->push([
                'type' => 'EMPTY',
                'period' => '',
                'customer' => '',
                'package' => '',
                'amount' => '',
                'date' => '',
                'invoice' => '',
                'status' => ''
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Periode',
            'Nama Pelanggan',
            'Paket',
            'Jumlah (Rp)',
            'Tanggal Bayar',
            'No. Invoice',
            'Status'
        ];
    }

    public function map($row): array
    {
        return [
            $row['period'],
            $row['customer'],
            $row['package'],
            $row['amount'] ? 'Rp ' . number_format($row['amount'], 0, ',', '.') : '',
            $row['date'],
            $row['invoice'],
            $row['status']
        ];
    }

    public function title(): string
    {
        $title = 'Laporan Pembayaran ' . $this->year;
        if ($this->month) {
            $monthName = \Carbon\Carbon::create($this->year, $this->month, 1)->format('F');
            $title .= ' - ' . $monthName;
        }
        return $title;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Periode
            'B' => 25, // Nama Pelanggan
            'C' => 20, // Paket
            'D' => 18, // Jumlah
            'E' => 15, // Tanggal
            'F' => 20, // Invoice
            'G' => 12, // Status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                
                // Apply borders to all data
                $sheet->getStyle('A1:G' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Style summary rows
                for ($row = 2; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell('A' . $row)->getValue();
                    
                    if (strpos($cellValue, 'RINGKASAN') !== false || 
                        strpos($cellValue, 'Total') !== false || 
                        strpos($cellValue, 'Rata-rata') !== false) {
                        $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'E7E6E6']
                            ],
                            'font' => ['bold' => true],
                        ]);
                    }
                    
                    // Style period headers
                    if (preg_match('/\b(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\b/', $cellValue)) {
                        $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'D9E1F2']
                            ],
                            'font' => ['bold' => true],
                        ]);
                    }
                }

                // Auto-fit columns
                foreach (range('A', 'G') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(false);
                }
            },
        ];
    }
}
