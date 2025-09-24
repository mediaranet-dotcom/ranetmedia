<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Customer::with(['payments' => function ($query) {
            $query->latest('created_at');
        }]);

        // Apply filters if provided
        if (!empty($this->filters)) {
            // Add filter logic here based on the filters array
            // This would match the filters from your table
        }

        return $query->get();
    }

    public function headings(): array
    {
        // Get selected year and month from filters
        $selectedYear = $this->filters['payment_year'] ?? now()->year;
        $selectedMonth = $this->filters['payment_month'] ?? now()->month;

        // Get month name in Indonesian
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $monthName = $monthNames[$selectedMonth] ?? 'Unknown';

        return [
            'ID',
            'Nama Customer',
            'Alamat',
            'No. Telepon',
            'Paket',
            'Harga Paket',
            'Status',
            "Pembayaran {$monthName} {$selectedYear}",
            'Pembayaran Terakhir',
            'Tanggal Daftar',
        ];
    }

    public function map($customer): array
    {
        // Get selected year and month from filters, default to current
        $selectedYear = $this->filters['payment_year'] ?? now()->year;
        $selectedMonth = $this->filters['payment_month'] ?? now()->month;

        // Calculate payment for selected period
        $periodPayment = $customer->payments()
            ->whereMonth('payments.created_at', $selectedMonth)
            ->whereYear('payments.created_at', $selectedYear)
            ->sum('amount');

        // Get last payment
        $lastPayment = $customer->payments()->latest('payment_date')->first();
        $lastPaymentDate = $lastPayment ? $lastPayment->payment_date->format('d/m/Y') : 'Belum ada';



        return [
            $customer->id,
            $customer->name,
            $customer->address,
            $customer->phone,
            $customer->package,
            $customer->package_price, // Raw number for Excel calculation
            $customer->status,
            $periodPayment, // Raw number for Excel calculation
            $lastPaymentDate,
            $customer->created_at->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],

            // Style the header row
            'A1:J1' => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '"Rp "#,##0', // Package price column as currency
            'H' => '"Rp "#,##0', // Payment amount column as currency
        ];
    }
}
