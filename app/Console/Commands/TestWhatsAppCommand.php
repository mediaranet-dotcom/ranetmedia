<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;
use App\Models\Invoice;
use App\Models\Customer;

class TestWhatsAppCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'whatsapp:test 
                            {--phone= : Phone number to test (optional)}
                            {--invoice= : Invoice ID to test (optional)}
                            {--show-urls : Show WhatsApp URLs for manual testing}';

    /**
     * The console command description.
     */
    protected $description = 'Test WhatsApp invoice sending functionality';

    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📱 Testing WhatsApp Invoice Sending...');
        
        if (!env('WHATSAPP_ENABLED', false)) {
            $this->error('❌ WhatsApp is disabled. Set WHATSAPP_ENABLED=true in .env');
            return 1;
        }

        $this->info('✅ WhatsApp is enabled');
        
        if (env('WHATSAPP_TEST_MODE', false)) {
            $this->warn('🧪 Running in TEST MODE - messages will be logged, not actually sent');
        }

        // Test with specific phone or invoice
        $phone = $this->option('phone');
        $invoiceId = $this->option('invoice');

        if ($phone && $invoiceId) {
            return $this->testSpecificInvoice($invoiceId, $phone);
        } elseif ($invoiceId) {
            return $this->testInvoiceById($invoiceId);
        } else {
            return $this->testWithSampleData();
        }
    }

    /**
     * Test with specific invoice and phone
     */
    protected function testSpecificInvoice(string $invoiceId, string $phone): int
    {
        $invoice = Invoice::find($invoiceId);
        
        if (!$invoice) {
            $this->error("❌ Invoice with ID {$invoiceId} not found");
            return 1;
        }

        $this->info("📄 Testing with Invoice: {$invoice->invoice_number}");
        $this->info("📞 Testing with Phone: {$phone}");

        // Temporarily override customer phone
        $originalPhone = $invoice->customer->phone;
        $invoice->customer->phone = $phone;

        $result = $this->whatsappService->sendInvoiceNotification($invoice);

        // Restore original phone
        $invoice->customer->phone = $originalPhone;

        return $this->showResult($result, $invoice, $phone);
    }

    /**
     * Test with specific invoice ID
     */
    protected function testInvoiceById(string $invoiceId): int
    {
        $invoice = Invoice::with('customer')->find($invoiceId);
        
        if (!$invoice) {
            $this->error("❌ Invoice with ID {$invoiceId} not found");
            return 1;
        }

        if (!$invoice->customer->phone) {
            $this->error("❌ Customer {$invoice->customer->name} doesn't have a phone number");
            return 1;
        }

        $this->info("📄 Testing with Invoice: {$invoice->invoice_number}");
        $this->info("👤 Customer: {$invoice->customer->name}");
        $this->info("📞 Phone: {$invoice->customer->phone}");

        $result = $this->whatsappService->sendInvoiceNotification($invoice);

        return $this->showResult($result, $invoice, $invoice->customer->phone);
    }

    /**
     * Test with sample data
     */
    protected function testWithSampleData(): int
    {
        // Find an invoice with customer phone
        $invoice = Invoice::with('customer')
            ->whereHas('customer', function ($query) {
                $query->whereNotNull('phone');
            })
            ->first();

        if (!$invoice) {
            $this->error('❌ No invoices found with customer phone numbers');
            $this->info('💡 Try: php artisan whatsapp:test --phone=6281234567890 --invoice=1');
            return 1;
        }

        $this->info("📄 Testing with Invoice: {$invoice->invoice_number}");
        $this->info("👤 Customer: {$invoice->customer->name}");
        $this->info("📞 Phone: {$invoice->customer->phone}");

        $result = $this->whatsappService->sendInvoiceNotification($invoice);

        return $this->showResult($result, $invoice, $invoice->customer->phone);
    }

    /**
     * Show test result
     */
    protected function showResult(bool $result, Invoice $invoice, string $phone): int
    {
        if ($result) {
            $this->info('✅ WhatsApp message sent successfully!');
            
            if (env('WHATSAPP_TEST_MODE', false)) {
                $this->showTestModeResults($phone);
            }
            
            return 0;
        } else {
            $this->error('❌ Failed to send WhatsApp message');
            $this->info('💡 Check the logs for more details:');
            $this->info('   tail -f storage/logs/laravel.log | grep -i whatsapp');
            return 1;
        }
    }

    /**
     * Show test mode results
     */
    protected function showTestModeResults(string $phone): void
    {
        $testMessages = \Cache::get('whatsapp_test_messages', []);
        
        if (empty($testMessages)) {
            $this->warn('⚠️ No test messages found in cache');
            return;
        }

        $this->info('');
        $this->info('🧪 TEST MODE RESULTS:');
        $this->info('');

        foreach ($testMessages as $msg) {
            if ($msg['phone'] === $this->formatPhoneNumber($phone)) {
                $this->info("📱 Phone: {$msg['phone']}");
                $this->info("📝 Message: " . substr($msg['message'], 0, 100) . '...');
                $this->info("🔗 WhatsApp URL: {$msg['url']}");
                $this->info("⏰ Time: {$msg['sent_at']}");
                $this->info('');
                
                if ($this->option('show-urls')) {
                    $this->info('🌐 Click this URL to open WhatsApp:');
                    $this->info($msg['url']);
                }
                break;
            }
        }
    }

    /**
     * Format phone number
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add country code if not present
        if (!str_starts_with($phone, '62')) {
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } else {
                $phone = '62' . $phone;
            }
        }
        
        return $phone;
    }
}
