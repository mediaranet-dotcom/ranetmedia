<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;
use App\Models\Invoice;
use Carbon\Carbon;

class SendWhatsAppNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'whatsapp:send 
                            {type : Type of notification (invoice|reminder|overdue)}
                            {--month= : Month for invoice notifications (1-12)}
                            {--year= : Year for invoice notifications}
                            {--dry-run : Show what would be sent without actually sending}
                            {--limit=50 : Maximum number of messages to send}';

    /**
     * The console command description.
     */
    protected $description = 'Send WhatsApp notifications for invoices and payment reminders';

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
        $type = $this->argument('type');
        $dryRun = $this->option('dry-run');
        $limit = (int) $this->option('limit');

        $this->info("📱 Starting WhatsApp {$type} notifications...");
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No messages will be actually sent');
        }

        try {
            switch ($type) {
                case 'invoice':
                    $this->sendInvoiceNotifications($dryRun, $limit);
                    break;
                case 'reminder':
                    $this->sendPaymentReminders($dryRun, $limit);
                    break;
                case 'overdue':
                    $this->sendOverdueReminders($dryRun, $limit);
                    break;
                default:
                    $this->error('❌ Invalid type. Use: invoice, reminder, or overdue');
                    return 1;
            }

            $this->info('✅ WhatsApp notifications completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Send invoice notifications
     */
    protected function sendInvoiceNotifications(bool $dryRun, int $limit): void
    {
        $month = $this->option('month') ?? now()->month;
        $year = $this->option('year') ?? now()->year;

        $this->info("📅 Sending invoice notifications for {$month}/{$year}...");

        $invoices = Invoice::with(['customer', 'service.package'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereIn('status', ['draft', 'sent'])
            ->whereHas('customer', function ($query) {
                $query->whereNotNull('phone');
            })
            ->limit($limit)
            ->get();

        if ($invoices->isEmpty()) {
            $this->warn('⚠️ No invoices found for the specified period');
            return;
        }

        $this->info("📋 Found {$invoices->count()} invoices to process");

        if ($dryRun) {
            $this->showInvoicePreview($invoices);
            return;
        }

        $this->sendBulkNotifications($invoices, 'invoice');
    }

    /**
     * Send payment reminders
     */
    protected function sendPaymentReminders(bool $dryRun, int $limit): void
    {
        $this->info('📢 Sending payment reminders...');

        // Get invoices due in 3 days
        $upcomingDue = Invoice::with(['customer', 'service.package'])
            ->where('status', '!=', 'paid')
            ->whereBetween('due_date', [now()->addDays(2), now()->addDays(4)])
            ->whereHas('customer', function ($query) {
                $query->whereNotNull('phone');
            })
            ->limit($limit)
            ->get();

        if ($upcomingDue->isEmpty()) {
            $this->warn('⚠️ No invoices due for reminders');
            return;
        }

        $this->info("📋 Found {$upcomingDue->count()} invoices due for reminders");

        if ($dryRun) {
            $this->showReminderPreview($upcomingDue, 'upcoming');
            return;
        }

        $this->sendBulkNotifications($upcomingDue, 'reminder');
    }

    /**
     * Send overdue reminders
     */
    protected function sendOverdueReminders(bool $dryRun, int $limit): void
    {
        $this->info('🚨 Sending overdue payment reminders...');

        $overdueInvoices = Invoice::with(['customer', 'service.package'])
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->whereHas('customer', function ($query) {
                $query->whereNotNull('phone');
            })
            ->limit($limit)
            ->get();

        if ($overdueInvoices->isEmpty()) {
            $this->warn('⚠️ No overdue invoices found');
            return;
        }

        $this->info("📋 Found {$overdueInvoices->count()} overdue invoices");

        if ($dryRun) {
            $this->showReminderPreview($overdueInvoices, 'overdue');
            return;
        }

        $this->sendBulkNotifications($overdueInvoices, 'reminder');
    }

    /**
     * Send bulk notifications with progress bar
     */
    protected function sendBulkNotifications($invoices, string $type): void
    {
        $progressBar = $this->output->createProgressBar($invoices->count());
        $progressBar->start();

        $results = [
            'sent' => 0,
            'failed' => 0,
            'queued' => 0,
            'skipped' => 0
        ];

        foreach ($invoices as $invoice) {
            try {
                $success = $type === 'reminder' 
                    ? $this->whatsappService->sendPaymentReminder($invoice)
                    : $this->whatsappService->sendInvoiceNotification($invoice);

                if ($success) {
                    $results['sent']++;
                } else {
                    $results['failed']++;
                }

                // Add delay to avoid rate limiting
                sleep(3);
                
            } catch (\Exception $e) {
                $results['failed']++;
                $this->error("Failed to send to {$invoice->customer->name}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Show results
        $this->displayResults($results);
        
        // Show usage stats
        $this->showUsageStats();
    }

    /**
     * Show invoice preview
     */
    protected function showInvoicePreview($invoices): void
    {
        $table = [];
        foreach ($invoices as $invoice) {
            $table[] = [
                $invoice->invoice_number,
                $invoice->customer->name,
                $invoice->customer->phone,
                'Rp ' . number_format($invoice->total_amount, 0, ',', '.'),
                $invoice->due_date->format('d/m/Y'),
                '✅ Will Send'
            ];
        }

        $this->table(['Invoice #', 'Customer', 'Phone', 'Amount', 'Due Date', 'Status'], $table);
    }

    /**
     * Show reminder preview
     */
    protected function showReminderPreview($invoices, string $reminderType): void
    {
        $table = [];
        foreach ($invoices as $invoice) {
            $daysOverdue = $reminderType === 'overdue' ? $invoice->getDaysOverdue() : 0;
            $table[] = [
                $invoice->invoice_number,
                $invoice->customer->name,
                $invoice->customer->phone,
                'Rp ' . number_format($invoice->outstanding_amount, 0, ',', '.'),
                $invoice->due_date->format('d/m/Y'),
                $reminderType === 'overdue' ? "{$daysOverdue} days" : 'Due soon',
                '✅ Will Send'
            ];
        }

        $this->table(['Invoice #', 'Customer', 'Phone', 'Amount', 'Due Date', 'Status', 'Action'], $table);
    }

    /**
     * Display results
     */
    protected function displayResults(array $results): void
    {
        $this->info("📊 WhatsApp Notification Results:");
        $this->line("   ✅ Sent: {$results['sent']}");
        $this->line("   ❌ Failed: {$results['failed']}");
        $this->line("   ⏳ Queued: {$results['queued']}");
        $this->line("   ⏭️ Skipped: {$results['skipped']}");
    }

    /**
     * Show WhatsApp usage statistics
     */
    protected function showUsageStats(): void
    {
        $stats = $this->whatsappService->getUsageStats();
        
        $this->newLine();
        $this->info("📱 WhatsApp Account Usage:");
        
        foreach ($stats as $accountName => $stat) {
            $status = $stat['available'] ? '✅ Available' : '❌ Rate Limited';
            $this->line("   {$accountName}: {$stat['daily_usage']}/{$stat['daily_limit']} daily, {$stat['hourly_usage']}/{$stat['hourly_limit']} hourly - {$status}");
        }
    }
}
