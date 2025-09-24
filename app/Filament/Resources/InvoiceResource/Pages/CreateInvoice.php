<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Check for duplicate invoice in the same billing period
        if (isset($data['service_id']) && isset($data['billing_period_start']) && isset($data['billing_period_end'])) {
            $existingInvoice = \App\Models\Invoice::where('service_id', $data['service_id'])
                ->where('billing_period_start', $data['billing_period_start'])
                ->where('billing_period_end', $data['billing_period_end'])
                ->first();

            if ($existingInvoice) {
                $periodStart = \Carbon\Carbon::parse($data['billing_period_start']);
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
                $monthName = $monthNames[$periodStart->month];
                $year = $periodStart->year;

                // Provide detailed information about existing invoice
                $statusLabel = match ($existingInvoice->status) {
                    'draft' => 'Draft',
                    'sent' => 'Terkirim',
                    'paid' => 'Lunas',
                    'overdue' => 'Terlambat',
                    'cancelled' => 'Dibatalkan',
                    default => ucfirst($existingInvoice->status)
                };

                $errorMessage = "❌ Invoice untuk periode {$monthName} {$year} sudah ada!\n\n";
                $errorMessage .= "📋 Detail Invoice yang sudah ada:\n";
                $errorMessage .= "• Nomor: {$existingInvoice->invoice_number}\n";
                $errorMessage .= "• Status: {$statusLabel}\n";
                $errorMessage .= "• Total: Rp " . number_format($existingInvoice->total_amount) . "\n";
                $errorMessage .= "• Tanggal: " . $existingInvoice->invoice_date->format('d/m/Y') . "\n\n";
                $errorMessage .= "💡 Solusi:\n";
                $errorMessage .= "1. Edit invoice yang sudah ada di menu Invoice\n";
                $errorMessage .= "2. Pilih periode billing yang berbeda\n";
                $errorMessage .= "3. Atau hapus invoice lama jika memang tidak diperlukan";

                throw new \Exception($errorMessage);
            }
        }

        // Ensure all required numeric fields have default values
        $data['subtotal'] = $data['subtotal'] ?? 0;
        $data['tax_rate'] = $data['tax_rate'] ?? 0;
        $data['tax_amount'] = $data['tax_amount'] ?? 0;
        $data['discount_amount'] = $data['discount_amount'] ?? 0;
        $data['total_amount'] = $data['total_amount'] ?? 0;
        $data['paid_amount'] = $data['paid_amount'] ?? 0;
        $data['outstanding_amount'] = $data['outstanding_amount'] ?? 0;

        // Calculate totals properly
        $subtotal = (float) $data['subtotal'];
        $taxRate = (float) $data['tax_rate'];
        $discountAmount = (float) $data['discount_amount'];
        $paidAmount = (float) $data['paid_amount'];

        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount - $discountAmount;
        $outstandingAmount = $totalAmount - $paidAmount;

        $data['tax_amount'] = $taxAmount;
        $data['total_amount'] = $totalAmount;
        $data['outstanding_amount'] = $outstandingAmount;

        return $data;
    }
}
