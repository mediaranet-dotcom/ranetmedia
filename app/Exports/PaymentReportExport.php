<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PaymentReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Payment::with(['invoice.customer'])
            ->orderBy('payment_date', 'desc');

        // Apply filters if provided
        if (!empty($this->filters['payment_method'])) {
            $query->where('payment_method', $this->filters['payment_method']);
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('payment_date', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('payment_date', '<=', $this->filters['to_date']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pembayaran',
            'No. Pelanggan',
            'Nama Pelanggan',
            'No. Invoice',
            'Jumlah Pembayaran',
            'Metode Pembayaran',
            'Jatuh Tempo',
            'Status Pembayaran',
            'No. Referensi',
            'Catatan'
        ];
    }

    public function map($payment): array
    {
        static $no = 1;

        $status = 'Tepat Waktu';
        if ($payment->invoice && $payment->payment_date > $payment->invoice->due_date) {
            $status = 'Terlambat';
        }

        return [
            $no++,
            $payment->payment_date->format('d/m/Y H:i'),
            $payment->invoice->customer->customer_number ?? 'N/A',
            $payment->invoice->customer->name ?? 'N/A',
            $payment->invoice->invoice_number ?? 'N/A',
            $payment->amount, // Raw number for Excel calculation
            ucfirst(str_replace('_', ' ', $payment->payment_method)),
            $payment->invoice ? $payment->invoice->due_date->format('d/m/Y') : 'N/A',
            $status,
            $payment->reference_number ?? '-',
            $payment->notes ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Style all cells
            'A:K' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Style amount column
            'F:F' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT
                ]
            ]
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '"Rp "#,##0', // Amount column as Indonesian Rupiah currency
        ];
    }
}
