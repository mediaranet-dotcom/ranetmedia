<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InvoiceService;
use Carbon\Carbon;

class GenerateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate {--date= : Date to generate invoices for (Y-m-d format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices for services that are due for billing';

    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        parent::__construct();
        $this->invoiceService = $invoiceService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : now();

        $this->info("Generating invoices for date: {$date->format('Y-m-d')}");

        $invoices = $this->invoiceService->generateDueInvoices($date);

        if ($invoices->isEmpty()) {
            $this->info('No invoices to generate.');
            return;
        }

        $this->info("Generated {$invoices->count()} invoices:");

        foreach ($invoices as $invoice) {
            $this->line("- {$invoice->invoice_number} for {$invoice->customer->name} (Rp " . number_format($invoice->total_amount, 0, ',', '.') . ")");
        }

        $this->info('Invoice generation completed successfully!');
    }
}
