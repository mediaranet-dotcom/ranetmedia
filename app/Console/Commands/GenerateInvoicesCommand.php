<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InvoiceService;
use App\Services\EmailService;
use Carbon\Carbon;

class GenerateInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'invoices:generate 
                            {--month= : Month to generate invoices for (1-12)}
                            {--year= : Year to generate invoices for}
                            {--send-email : Send email notifications to customers}
                            {--dry-run : Show what would be generated without actually creating invoices}';

    /**
     * The console command description.
     */
    protected $description = 'Generate invoices for all active services that are due for billing';

    protected InvoiceService $invoiceService;
    protected EmailService $emailService;

    public function __construct(InvoiceService $invoiceService, EmailService $emailService)
    {
        parent::__construct();
        $this->invoiceService = $invoiceService;
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Auto Invoice Generation...');
        
        $month = $this->option('month');
        $year = $this->option('year');
        $sendEmail = $this->option('send-email');
        $dryRun = $this->option('dry-run');

        try {
            if ($month && $year) {
                // Generate for specific month/year
                $this->generateForSpecificPeriod($month, $year, $sendEmail, $dryRun);
            } else {
                // Generate for current due invoices
                $this->generateDueInvoices($sendEmail, $dryRun);
            }

            $this->info('✅ Auto Invoice Generation completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('❌ Error during invoice generation: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Generate invoices for specific month/year
     */
    private function generateForSpecificPeriod(int $month, int $year, bool $sendEmail, bool $dryRun): void
    {
        $this->info("📅 Generating invoices for {$month}/{$year}...");

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No invoices will be actually created');
            $this->showWhatWouldBeGenerated($month, $year);
            return;
        }

        $invoices = $this->invoiceService->generateMonthlyInvoices($month, $year);

        $this->info("📊 Generated {$invoices->count()} invoices");

        if ($invoices->count() > 0) {
            $this->displayInvoicesSummary($invoices);

            if ($sendEmail) {
                $this->sendEmailNotifications($invoices);
            }
        } else {
            $this->warn('⚠️ No invoices were generated. All services may already have invoices for this period.');
        }
    }

    /**
     * Generate due invoices
     */
    private function generateDueInvoices(bool $sendEmail, bool $dryRun): void
    {
        $this->info('📅 Generating invoices for services due for billing...');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No invoices will be actually created');
            $this->showWhatWouldBeGeneratedDue();
            return;
        }

        $invoices = $this->invoiceService->generateDueInvoices();

        $this->info("📊 Generated {$invoices->count()} invoices");

        if ($invoices->count() > 0) {
            $this->displayInvoicesSummary($invoices);

            if ($sendEmail) {
                $this->sendEmailNotifications($invoices);
            }
        } else {
            $this->warn('⚠️ No invoices were generated. No services are currently due for billing.');
        }
    }

    /**
     * Show what would be generated (dry run)
     */
    private function showWhatWouldBeGenerated(int $month, int $year): void
    {
        $services = \App\Models\Service::with(['customer', 'package', 'billingCycle'])
            ->where('status', 'active')
            ->where('auto_billing', true)
            ->whereHas('billingCycle', function ($query) {
                $query->where('interval_type', 'month')
                      ->where('interval_count', 1);
            })
            ->get();

        $this->info("🔍 Services that would get invoices for {$month}/{$year}:");

        $table = [];
        foreach ($services as $service) {
            // Check if invoice already exists
            $existingInvoice = \App\Models\Invoice::where('service_id', $service->id)
                ->whereMonth('billing_period_start', $month)
                ->whereYear('billing_period_start', $year)
                ->first();

            if (!$existingInvoice) {
                $table[] = [
                    $service->customer->name,
                    $service->package->name,
                    'Rp ' . number_format($service->monthly_fee ?? $service->package->price, 0, ',', '.'),
                    $service->billingCycle->name ?? 'No Cycle',
                    '✅ Will Generate'
                ];
            }
        }

        if (empty($table)) {
            $this->warn('⚠️ No invoices would be generated. All services already have invoices for this period.');
        } else {
            $this->table(['Customer', 'Package', 'Amount', 'Billing Cycle', 'Status'], $table);
        }
    }

    /**
     * Show what would be generated for due invoices (dry run)
     */
    private function showWhatWouldBeGeneratedDue(): void
    {
        $date = now();
        $services = \App\Models\Service::with(['customer', 'package', 'billingCycle'])
            ->where('status', 'active')
            ->where('auto_billing', true)
            ->where(function ($query) use ($date) {
                $query->whereNull('next_billing_date')
                    ->orWhere('next_billing_date', '<=', $date);
            })
            ->get();

        $this->info('🔍 Services that would get invoices (due for billing):');

        $table = [];
        foreach ($services as $service) {
            $table[] = [
                $service->customer->name,
                $service->package->name,
                'Rp ' . number_format($service->monthly_fee ?? $service->package->price, 0, ',', '.'),
                $service->billingCycle->name ?? 'No Cycle',
                $service->next_billing_date ? $service->next_billing_date->format('d/m/Y') : 'Not Set',
                '✅ Will Generate'
            ];
        }

        if (empty($table)) {
            $this->warn('⚠️ No invoices would be generated. No services are currently due for billing.');
        } else {
            $this->table(['Customer', 'Package', 'Amount', 'Billing Cycle', 'Next Billing', 'Status'], $table);
        }
    }

    /**
     * Display invoices summary
     */
    private function displayInvoicesSummary($invoices): void
    {
        $table = [];
        $totalAmount = 0;

        foreach ($invoices as $invoice) {
            $table[] = [
                $invoice->invoice_number,
                $invoice->customer->name,
                $invoice->service->package->name,
                'Rp ' . number_format($invoice->total_amount, 0, ',', '.'),
                $invoice->due_date->format('d/m/Y'),
                $invoice->status
            ];
            $totalAmount += $invoice->total_amount;
        }

        $this->table(['Invoice #', 'Customer', 'Package', 'Amount', 'Due Date', 'Status'], $table);
        $this->info("💰 Total Amount: Rp " . number_format($totalAmount, 0, ',', '.'));
    }

    /**
     * Send email notifications
     */
    private function sendEmailNotifications($invoices): void
    {
        $this->info('📧 Sending email notifications...');
        
        $sent = 0;
        $failed = 0;

        foreach ($invoices as $invoice) {
            try {
                $this->emailService->sendInvoiceEmail($invoice);
                $sent++;
                $this->line("✅ Email sent to {$invoice->customer->name}");
            } catch (\Exception $e) {
                $failed++;
                $this->error("❌ Failed to send email to {$invoice->customer->name}: " . $e->getMessage());
            }
        }

        $this->info("📧 Email Summary: {$sent} sent, {$failed} failed");
    }
}
