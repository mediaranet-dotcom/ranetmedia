<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Auto generate invoices for services due for billing (daily at 6 AM)
        $schedule->command('invoices:generate --send-email')
            ->dailyAt('06:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/auto-invoice.log'));

        // Generate monthly invoices on the 1st of each month at 7 AM
        $schedule->command('invoices:generate --month=' . now()->month . ' --year=' . now()->year . ' --send-email')
            ->monthlyOn(1, '07:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/monthly-invoice.log'));

        // Send payment reminders for overdue invoices (daily at 9 AM)
        $schedule->call(function () {
            $emailService = app(\App\Services\EmailService::class);
            $results = $emailService->sendOverdueReminders();
            Log::info('Scheduled overdue reminders sent', $results);
        })
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Send gentle reminders 3 days before due date (daily at 8 AM)
        $schedule->call(function () {
            $upcomingDue = \App\Models\Invoice::with(['customer', 'service'])
                ->where('status', '!=', 'paid')
                ->whereBetween('due_date', [now()->addDays(2), now()->addDays(4)])
                ->whereHas('customer', function ($query) {
                    $query->whereNotNull('email');
                })
                ->get();

            $emailService = app(\App\Services\EmailService::class);
            $sent = 0;

            foreach ($upcomingDue as $invoice) {
                try {
                    // Send gentle reminder for upcoming due date
                    $emailService->sendPaymentReminder($invoice);
                    $sent++;
                } catch (\Exception $e) {
                    Log::error("Failed to send upcoming due reminder: " . $e->getMessage());
                }
            }

            Log::info("Sent {$sent} upcoming due date reminders");
        })
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Update invoice status to overdue (daily at midnight)
        $schedule->call(function () {
            $updated = \App\Models\Invoice::where('status', '!=', 'paid')
                ->where('due_date', '<', now()->startOfDay())
                ->where('status', '!=', 'overdue')
                ->update(['status' => 'overdue']);

            Log::info("Updated {$updated} invoices to overdue status");
        })
            ->daily()
            ->withoutOverlapping();

        // Send WhatsApp invoice notifications (daily at 10 AM)
        $schedule->command('whatsapp:send invoice --limit=100')
            ->dailyAt('10:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/whatsapp-invoice.log'));

        // Send WhatsApp payment reminders (daily at 2 PM)
        $schedule->command('whatsapp:send reminder --limit=50')
            ->dailyAt('14:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/whatsapp-reminder.log'));

        // Send WhatsApp overdue reminders (daily at 4 PM)
        $schedule->command('whatsapp:send overdue --limit=50')
            ->dailyAt('16:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/whatsapp-overdue.log'));

        // Clean up old logs (weekly)
        $schedule->call(function () {
            $logFiles = [
                storage_path('logs/auto-invoice.log'),
                storage_path('logs/monthly-invoice.log'),
                storage_path('logs/whatsapp-invoice.log'),
                storage_path('logs/whatsapp-reminder.log'),
                storage_path('logs/whatsapp-overdue.log'),
            ];

            foreach ($logFiles as $logFile) {
                if (file_exists($logFile) && filesize($logFile) > 10 * 1024 * 1024) { // 10MB
                    file_put_contents($logFile, ''); // Clear the file
                }
            }
        })
            ->weekly()
            ->sundays()
            ->at('02:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
