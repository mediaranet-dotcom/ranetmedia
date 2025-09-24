<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\Payment;

class FixInvoiceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:fix-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invoice status based on completed payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing invoice status based on payments...');

        // Get all invoices that have completed payments but wrong status
        $invoicesWithPayments = Invoice::whereHas('payments', function ($query) {
            $query->where('status', 'completed');
        })->get();

        $fixed = 0;

        foreach ($invoicesWithPayments as $invoice) {
            $oldStatus = $invoice->status;
            $oldPaidAmount = $invoice->paid_amount;
            $oldOutstanding = $invoice->outstanding_amount;

            // Get any payment for this invoice to trigger the update
            $payment = $invoice->payments()->where('status', 'completed')->first();
            if ($payment) {
                $payment->updateInvoiceStatus();
                $invoice->refresh();

                if ($oldStatus !== $invoice->status || $oldPaidAmount != $invoice->paid_amount) {
                    $this->line("✅ Fixed {$invoice->invoice_number}: {$oldStatus} → {$invoice->status} (Paid: Rp " . number_format($invoice->paid_amount, 0, ',', '.') . ")");
                    $fixed++;
                }
            }
        }

        $this->info("🎉 Fixed {$fixed} invoices!");
        return 0;
    }
}
