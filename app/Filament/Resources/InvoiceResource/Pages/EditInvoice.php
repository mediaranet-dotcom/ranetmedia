<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn() => route('invoice.download', $this->record))
                ->openUrlInNewTab(),
            Actions\Action::make('view_pdf')
                ->label('Lihat PDF')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn() => route('invoice.pdf', $this->record))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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
