<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\BillingCycle;
use App\Services\InvoiceService;

class TestInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $invoiceService = new InvoiceService();
        
        // Get default billing cycle (monthly)
        $billingCycle = BillingCycle::where('name', 'Bulanan')->first();
        
        if (!$billingCycle) {
            $this->command->error('Billing cycle not found. Please run BillingCycleSeeder first.');
            return;
        }
        
        // Update all services to have billing cycle
        $services = Service::where('status', 'active')->get();
        
        foreach ($services as $service) {
            // Set billing cycle and next billing date
            $service->update([
                'billing_cycle_id' => $billingCycle->id,
                'billing_day' => 1,
                'next_billing_date' => now()->startOfMonth(),
                'auto_billing' => true,
            ]);
            
            try {
                // Generate invoice for this service
                $invoice = $invoiceService->generateInvoiceForService($service);
                $this->command->info("Generated invoice {$invoice->invoice_number} for {$service->customer->name}");
                
                // Mark some invoices as sent
                if (rand(1, 3) > 1) {
                    $invoiceService->markAsSent($invoice);
                    $this->command->info("Marked invoice {$invoice->invoice_number} as sent");
                }
                
                // Simulate some payments
                if (rand(1, 4) > 1) {
                    $paymentAmount = rand(50, 100) / 100 * $invoice->total_amount;
                    $invoiceService->processPayment($invoice, $paymentAmount, [
                        'payment_method' => ['cash', 'transfer', 'e_wallet'][rand(0, 2)],
                        'payment_notes' => 'Test payment via seeder',
                    ]);
                    $this->command->info("Added payment of Rp " . number_format($paymentAmount, 0, ',', '.') . " to invoice {$invoice->invoice_number}");
                }
                
            } catch (\Exception $e) {
                $this->command->error("Failed to generate invoice for service {$service->id}: " . $e->getMessage());
            }
        }
        
        $this->command->info('Test invoices generated successfully!');
    }
}
