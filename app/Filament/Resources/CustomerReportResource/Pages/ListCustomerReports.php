<?php

namespace App\Filament\Resources\CustomerReportResource\Pages;

use App\Filament\Resources\CustomerReportResource;
use App\Exports\CustomerReportExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListCustomerReports extends ListRecords
{
    protected static string $resource = CustomerReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('monthly_summary')
                ->label('Ringkasan Bulan Ini')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->action(function () {
                    $this->dispatch('open-modal', id: 'monthly-summary');
                }),
            Actions\Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    try {
                        // Get current filter values
                        $filters = [
                            'payment_year' => request()->get('tableFilters.payment_year.value', now()->year),
                            'payment_month' => request()->get('tableFilters.payment_month.value', now()->month),
                        ];

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
                        $monthName = $monthNames[$filters['payment_month']] ?? 'Unknown';

                        $fileName = "laporan-pembayaran-{$monthName}-{$filters['payment_year']}-" . now()->format('d-m-Y-H-i-s') . '.xlsx';

                        return Excel::download(
                            new CustomerReportExport($filters),
                            $fileName
                        );
                    } catch (\Exception $e) {
                        $this->notify('danger', 'Error saat export: ' . $e->getMessage());
                        return null;
                    }
                }),
            Actions\Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-document-text')
                ->color('warning')
                ->action(function () {
                    try {
                        // Get current filter values
                        $filters = [
                            'payment_year' => request()->get('tableFilters.payment_year.value', now()->year),
                            'payment_month' => request()->get('tableFilters.payment_month.value', now()->month),
                        ];

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
                        $monthName = $monthNames[$filters['payment_month']] ?? 'Unknown';

                        $fileName = "laporan-pembayaran-{$monthName}-{$filters['payment_year']}-" . now()->format('d-m-Y-H-i-s') . '.csv';

                        return Excel::download(
                            new CustomerReportExport($filters),
                            $fileName,
                            \Maatwebsite\Excel\Excel::CSV
                        );
                    } catch (\Exception $e) {
                        $this->notify('danger', 'Error saat export CSV: ' . $e->getMessage());
                        return null;
                    }
                }),
            Actions\Action::make('refresh')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->action(fn() => $this->redirect(request()->header('Referer'))),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Widgets disabled temporarily to fix component error
        ];
    }
}
