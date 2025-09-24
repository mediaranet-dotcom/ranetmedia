<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class CleanupInvoicesCommand extends Command
{
    protected $signature = 'invoices:cleanup 
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--empty-drafts : Delete empty draft invoices}
                            {--duplicates : Show duplicate invoices for same service and period}';

    protected $description = 'Cleanup invoice duplicates and empty drafts';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $cleanEmptyDrafts = $this->option('empty-drafts');
        $showDuplicates = $this->option('duplicates');

        $this->info('🧹 Starting Invoice Cleanup...');

        if ($cleanEmptyDrafts) {
            $this->cleanupEmptyDrafts($dryRun);
        }

        if ($showDuplicates) {
            $this->showDuplicates();
        }

        if (!$cleanEmptyDrafts && !$showDuplicates) {
            $this->info('Please specify an option:');
            $this->info('  --empty-drafts    Clean empty draft invoices');
            $this->info('  --duplicates      Show duplicate invoices');
            $this->info('  --dry-run         Preview changes without executing');
        }

        return 0;
    }

    private function cleanupEmptyDrafts(bool $dryRun = false)
    {
        $this->info('🔍 Looking for empty draft invoices...');

        $emptyDrafts = Invoice::where('status', 'draft')
            ->where('total_amount', 0)
            ->whereDoesntHave('items')
            ->get();

        if ($emptyDrafts->isEmpty()) {
            $this->info('✅ No empty draft invoices found.');
            return;
        }

        $this->info("Found {$emptyDrafts->count()} empty draft invoices:");

        $headers = ['Invoice Number', 'Customer', 'Date', 'Total'];
        $rows = [];

        foreach ($emptyDrafts as $invoice) {
            $rows[] = [
                $invoice->invoice_number,
                $invoice->customer->name,
                $invoice->invoice_date->format('d/m/Y'),
                'Rp ' . number_format($invoice->total_amount)
            ];
        }

        $this->table($headers, $rows);

        if ($dryRun) {
            $this->warn('🔍 DRY RUN - No invoices were actually deleted');
            return;
        }

        if ($this->confirm('Delete these empty draft invoices?')) {
            $deleted = 0;
            foreach ($emptyDrafts as $invoice) {
                $invoice->delete();
                $deleted++;
            }

            $this->info("✅ Deleted {$deleted} empty draft invoices");
        } else {
            $this->info('❌ Cleanup cancelled');
        }
    }

    private function showDuplicates()
    {
        $this->info('🔍 Looking for duplicate invoices...');

        $duplicates = Invoice::selectRaw('service_id, billing_period_start, billing_period_end, COUNT(*) as count')
            ->groupBy('service_id', 'billing_period_start', 'billing_period_end')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicate invoices found.');
            return;
        }

        $this->warn("Found {$duplicates->count()} sets of duplicate invoices:");

        foreach ($duplicates as $duplicate) {
            $invoices = Invoice::where('service_id', $duplicate->service_id)
                ->where('billing_period_start', $duplicate->billing_period_start)
                ->where('billing_period_end', $duplicate->billing_period_end)
                ->with(['customer', 'service.package'])
                ->get();

            $this->info("\n📋 Duplicate set for Service ID {$duplicate->service_id}:");
            $this->info("   Customer: {$invoices->first()->customer->name}");
            $this->info("   Package: {$invoices->first()->service->package->name}");
            $this->info("   Period: {$duplicate->billing_period_start} to {$duplicate->billing_period_end}");

            $headers = ['Invoice Number', 'Status', 'Total', 'Date', 'Action'];
            $rows = [];

            foreach ($invoices as $invoice) {
                $action = '';
                if ($invoice->status === 'draft' && $invoice->total_amount == 0) {
                    $action = '🗑️ Can delete';
                } elseif ($invoice->status === 'draft') {
                    $action = '⚠️ Draft with amount';
                } else {
                    $action = '✅ Keep';
                }

                $rows[] = [
                    $invoice->invoice_number,
                    ucfirst($invoice->status),
                    'Rp ' . number_format($invoice->total_amount),
                    $invoice->invoice_date->format('d/m/Y'),
                    $action
                ];
            }

            $this->table($headers, $rows);

            $this->info("💡 Suggested action:");
            $draftsWithAmount = $invoices->where('status', 'draft')->where('total_amount', '>', 0);
            $emptyDrafts = $invoices->where('status', 'draft')->where('total_amount', 0);
            $nonDrafts = $invoices->where('status', '!=', 'draft');

            if ($emptyDrafts->count() > 0) {
                $this->info("   - Delete empty drafts: " . $emptyDrafts->pluck('invoice_number')->join(', '));
            }
            if ($draftsWithAmount->count() > 1) {
                $this->warn("   - Review drafts with amounts manually");
            }
            if ($nonDrafts->count() > 1) {
                $this->error("   - Multiple non-draft invoices - requires manual review!");
            }
        }

        $this->info("\n🛠️ To clean up duplicates, you can:");
        $this->info("   1. Use: php artisan invoices:cleanup --empty-drafts");
        $this->info("   2. Manually delete unwanted invoices from admin panel");
        $this->info("   3. Edit existing invoices instead of creating new ones");
    }
}
