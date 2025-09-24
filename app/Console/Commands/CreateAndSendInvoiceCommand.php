<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InvoiceService;
use App\Services\WhatsAppService;
use App\Models\Service;
use App\Models\Customer;
use Carbon\Carbon;

class CreateAndSendInvoiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'invoice:create-and-send 
                            {--service= : Service ID to create invoice for}
                            {--customer= : Customer ID to create invoice for}
                            {--month= : Month (1-12)}
                            {--year= : Year}
                            {--send-whatsapp : Send via WhatsApp after creation}
                            {--dry-run : Show what would be created without actually creating}';

    /**
     * The console command description.
     */
    protected $description = 'Create invoice and optionally send via WhatsApp';

    protected InvoiceService $invoiceService;
    protected WhatsAppService $whatsappService;

    public function __construct(InvoiceService $invoiceService, WhatsAppService $whatsappService)
    {
        parent::__construct();
        $this->invoiceService = $invoiceService;
        $this->whatsappService = $whatsappService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📄 Creating Invoice and Sending via WhatsApp...');
        
        $serviceId = $this->option('service');
        $customerId = $this->option('customer');
        $month = $this->option('month') ?? now()->month;
        $year = $this->option('year') ?? now()->year;
        $sendWhatsApp = $this->option('send-whatsapp');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No invoice will be actually created');
        }

        try {
            if ($serviceId) {
                return $this->createForService($serviceId, $month, $year, $sendWhatsApp, $dryRun);
            } elseif ($customerId) {
                return $this->createForCustomer($customerId, $month, $year, $sendWhatsApp, $dryRun);
            } else {
                return $this->createForAllServices($month, $year, $sendWhatsApp, $dryRun);
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Create invoice for specific service
     */
    protected function createForService(int $serviceId, int $month, int $year, bool $sendWhatsApp, bool $dryRun): int
    {
        $service = Service::with(['customer', 'package'])->find($serviceId);
        
        if (!$service) {
            $this->error("❌ Service with ID {$serviceId} not found");
            return 1;
        }

        $this->info("🎯 Creating invoice for service: {$service->customer->name} - {$service->package->name}");

        if ($dryRun) {
            $this->showDryRunInfo($service, $month, $year);
            return 0;
        }

        $billingDate = Carbon::create($year, $month, 1);
        $invoice = $this->invoiceService->generateInvoiceForService($service, $billingDate);

        $this->info("✅ Invoice created: {$invoice->invoice_number}");
        $this->showInvoiceDetails($invoice);

        if ($sendWhatsApp) {
            return $this->sendWhatsAppNotification($invoice);
        }

        return 0;
    }

    /**
     * Create invoices for all services of a customer
     */
    protected function createForCustomer(int $customerId, int $month, int $year, bool $sendWhatsApp, bool $dryRun): int
    {
        $customer = Customer::with('services.package')->find($customerId);
        
        if (!$customer) {
            $this->error("❌ Customer with ID {$customerId} not found");
            return 1;
        }

        $services = $customer->services()->where('status', 'active')->get();
        
        if ($services->isEmpty()) {
            $this->error("❌ No active services found for customer {$customer->name}");
            return 1;
        }

        $this->info("🎯 Creating invoices for customer: {$customer->name} ({$services->count()} services)");

        $createdInvoices = [];
        $billingDate = Carbon::create($year, $month, 1);

        foreach ($services as $service) {
            if ($dryRun) {
                $this->showDryRunInfo($service, $month, $year);
                continue;
            }

            try {
                $invoice = $this->invoiceService->generateInvoiceForService($service, $billingDate);
                $createdInvoices[] = $invoice;
                $this->info("✅ Invoice created: {$invoice->invoice_number} for {$service->package->name}");
            } catch (\Exception $e) {
                $this->error("❌ Failed to create invoice for {$service->package->name}: " . $e->getMessage());
            }
        }

        if ($sendWhatsApp && !$dryRun) {
            foreach ($createdInvoices as $invoice) {
                $this->sendWhatsAppNotification($invoice);
                sleep(2); // Delay between messages
            }
        }

        return 0;
    }

    /**
     * Create invoices for all active services
     */
    protected function createForAllServices(int $month, int $year, bool $sendWhatsApp, bool $dryRun): int
    {
        $this->info("🎯 Creating invoices for all active services for {$month}/{$year}");

        if ($dryRun) {
            $this->warn('🔍 This would generate invoices for all active services');
            return 0;
        }

        $invoices = $this->invoiceService->generateMonthlyInvoices($month, $year);

        $this->info("✅ Generated {$invoices->count()} invoices");

        if ($sendWhatsApp && $invoices->count() > 0) {
            $this->info('📱 Sending WhatsApp notifications...');
            
            $results = $this->whatsappService->sendBulkNotifications($invoices->toArray(), 'invoice');
            
            $this->info("📊 WhatsApp Results:");
            $this->info("   ✅ Sent: {$results['sent']}");
            $this->info("   ❌ Failed: {$results['failed']}");
            $this->info("   ⏳ Queued: {$results['queued']}");
        }

        return 0;
    }

    /**
     * Send WhatsApp notification for invoice
     */
    protected function sendWhatsAppNotification($invoice): int
    {
        if (!$invoice->customer->phone) {
            $this->warn("⚠️ Customer {$invoice->customer->name} doesn't have a phone number");
            return 0;
        }

        $this->info("📱 Sending WhatsApp to {$invoice->customer->name} ({$invoice->customer->phone})...");

        $result = $this->whatsappService->sendInvoiceNotification($invoice);

        if ($result) {
            if (env('WHATSAPP_TEST_MODE', false)) {
                $testMessages = \Cache::get('whatsapp_test_messages', []);
                $latestMessage = end($testMessages);
                
                $this->info("✅ WhatsApp test message created");
                $this->info("🔗 URL: {$latestMessage['url']}");
            } else {
                $this->info("✅ WhatsApp sent successfully");
            }
        } else {
            $this->error("❌ Failed to send WhatsApp");
        }

        return 0;
    }

    /**
     * Show dry run information
     */
    protected function showDryRunInfo($service, int $month, int $year): void
    {
        $this->info("Would create invoice for:");
        $this->info("  Customer: {$service->customer->name}");
        $this->info("  Service: {$service->package->name}");
        $this->info("  Period: {$month}/{$year}");
        $this->info("  Amount: Rp " . number_format($service->monthly_fee ?? $service->package->price, 0, ',', '.'));
        $this->info("");
    }

    /**
     * Show invoice details
     */
    protected function showInvoiceDetails($invoice): void
    {
        $this->info("📋 Invoice Details:");
        $this->info("   Number: {$invoice->invoice_number}");
        $this->info("   Customer: {$invoice->customer->name}");
        $this->info("   Amount: Rp " . number_format($invoice->total_amount, 0, ',', '.'));
        $this->info("   Due Date: {$invoice->due_date->format('d/m/Y')}");
        $this->info("   Status: {$invoice->status}");
        $this->info("");
    }
}
