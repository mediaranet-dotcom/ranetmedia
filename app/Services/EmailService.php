<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send invoice email to customer
     */
    public function sendInvoiceEmail(Invoice $invoice): bool
    {
        try {
            $customer = $invoice->customer;

            if (!$customer->email) {
                Log::warning("Customer {$customer->name} (ID: {$customer->id}) does not have an email address");
                return false;
            }

            // Generate PDF invoice
            $invoiceService = app(InvoiceService::class);
            $pdfPath = $invoiceService->savePDF($invoice);

            $data = [
                'invoice' => $invoice,
                'customer' => $customer,
                'service' => $invoice->service,
                'package' => $invoice->service->package,
                'company_name' => config('app.name', 'ISP Provider'),
                'company_email' => config('mail.from.address'),
                'company_phone' => config('app.company_phone', ''),
            ];

            Mail::send('emails.invoice', $data, function ($message) use ($customer, $invoice, $pdfPath) {
                $message->to($customer->email, $customer->name)
                    ->subject("Invoice #{$invoice->invoice_number} - " . config('app.name'))
                    ->attach(storage_path('app/' . $pdfPath));
            });

            // Update invoice sent status
            $invoice->update([
                'sent_at' => now(),
                'status' => $invoice->status === 'draft' ? 'sent' : $invoice->status
            ]);

            Log::info("Invoice email sent successfully to {$customer->name} ({$customer->email})");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send invoice email to {$customer->name}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send payment reminder email
     */
    public function sendPaymentReminder(Invoice $invoice): bool
    {
        try {
            $customer = $invoice->customer;

            if (!$customer->email) {
                Log::warning("Customer {$customer->name} (ID: {$customer->id}) does not have an email address");
                return false;
            }

            $daysOverdue = $invoice->getDaysOverdue();
            $urgencyLevel = $this->getUrgencyLevel($daysOverdue);

            $data = [
                'invoice' => $invoice,
                'customer' => $customer,
                'service' => $invoice->service,
                'package' => $invoice->service->package,
                'days_overdue' => $daysOverdue,
                'urgency_level' => $urgencyLevel,
                'company_name' => config('app.name', 'ISP Provider'),
                'company_email' => config('mail.from.address'),
                'company_phone' => config('app.company_phone', ''),
            ];

            $subject = $this->getReminderSubject($urgencyLevel, $invoice->invoice_number);

            Mail::send('emails.payment-reminder', $data, function ($message) use ($customer, $subject) {
                $message->to($customer->email, $customer->name)
                    ->subject($subject);
            });

            Log::info("Payment reminder sent to {$customer->name} ({$customer->email}) for invoice {$invoice->invoice_number}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send payment reminder to {$customer->name}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send bulk payment reminders for overdue invoices
     */
    public function sendOverdueReminders(): array
    {
        $overdueInvoices = Invoice::with(['customer', 'service.package'])
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->whereHas('customer', function ($query) {
                $query->whereNotNull('email');
            })
            ->get();

        $results = [
            'sent' => 0,
            'failed' => 0,
            'skipped' => 0,
            'whatsapp_sent' => 0,
            'whatsapp_failed' => 0,
            'details' => []
        ];

        $whatsappService = app(WhatsAppService::class);

        foreach ($overdueInvoices as $invoice) {
            try {
                // Check if reminder was sent recently (within 3 days)
                if ($this->wasReminderSentRecently($invoice)) {
                    $results['skipped']++;
                    $results['details'][] = [
                        'invoice' => $invoice->invoice_number,
                        'customer' => $invoice->customer->name,
                        'status' => 'skipped',
                        'reason' => 'Reminder sent recently'
                    ];
                    continue;
                }

                // Send email reminder
                $emailSent = $this->sendPaymentReminder($invoice);

                // Send WhatsApp reminder if customer has phone
                $whatsappSent = false;
                if ($invoice->customer->phone) {
                    try {
                        $whatsappSent = $whatsappService->sendPaymentReminder($invoice);
                        if ($whatsappSent) {
                            $results['whatsapp_sent']++;
                        } else {
                            $results['whatsapp_failed']++;
                        }
                    } catch (\Exception $e) {
                        $results['whatsapp_failed']++;
                        Log::error("WhatsApp reminder failed for {$invoice->customer->name}: " . $e->getMessage());
                    }
                }

                if ($emailSent) {
                    $results['sent']++;
                }

                $results['details'][] = [
                    'invoice' => $invoice->invoice_number,
                    'customer' => $invoice->customer->name,
                    'status' => 'sent',
                    'email_sent' => $emailSent,
                    'whatsapp_sent' => $whatsappSent,
                    'days_overdue' => $invoice->getDaysOverdue()
                ];

                // Log reminder sent
                $this->logReminderSent($invoice);
            } catch (\Exception $e) {
                $results['failed']++;
                $results['details'][] = [
                    'invoice' => $invoice->invoice_number,
                    'customer' => $invoice->customer->name,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            }
        }

        Log::info("Bulk payment reminders completed. Email sent: {$results['sent']}, WhatsApp sent: {$results['whatsapp_sent']}, Failed: {$results['failed']}, Skipped: {$results['skipped']}");

        return $results;
    }

    /**
     * Get urgency level based on days overdue
     */
    private function getUrgencyLevel(int $daysOverdue): string
    {
        return match (true) {
            $daysOverdue <= 3 => 'gentle',
            $daysOverdue <= 7 => 'reminder',
            $daysOverdue <= 14 => 'urgent',
            default => 'final'
        };
    }

    /**
     * Get reminder subject based on urgency level
     */
    private function getReminderSubject(string $urgencyLevel, string $invoiceNumber): string
    {
        $companyName = config('app.name', 'ISP Provider');

        return match ($urgencyLevel) {
            'gentle' => "Pengingat Pembayaran - Invoice #{$invoiceNumber} - {$companyName}",
            'reminder' => "Pengingat Pembayaran Terlambat - Invoice #{$invoiceNumber} - {$companyName}",
            'urgent' => "URGENT: Pembayaran Terlambat - Invoice #{$invoiceNumber} - {$companyName}",
            'final' => "PERINGATAN TERAKHIR: Pembayaran Terlambat - Invoice #{$invoiceNumber} - {$companyName}",
            default => "Pengingat Pembayaran - Invoice #{$invoiceNumber} - {$companyName}"
        };
    }

    /**
     * Check if reminder was sent recently
     */
    private function wasReminderSentRecently(Invoice $invoice): bool
    {
        // Check if there's a log entry for reminder sent within last 3 days
        // This is a simple implementation - you might want to use a dedicated table for tracking
        return false; // For now, always send reminders
    }

    /**
     * Log reminder sent
     */
    private function logReminderSent(Invoice $invoice): void
    {
        // Log to database or file that reminder was sent
        // You can create a dedicated table for this if needed
        Log::info("Payment reminder logged for invoice {$invoice->invoice_number}");
    }

    /**
     * Send service activation email
     */
    public function sendServiceActivationEmail(Customer $customer, $service): bool
    {
        try {
            if (!$customer->email) {
                return false;
            }

            $data = [
                'customer' => $customer,
                'service' => $service,
                'package' => $service->package,
                'company_name' => config('app.name', 'ISP Provider'),
            ];

            Mail::send('emails.service-activation', $data, function ($message) use ($customer) {
                $message->to($customer->email, $customer->name)
                    ->subject('Layanan Internet Anda Telah Aktif - ' . config('app.name'));
            });

            Log::info("Service activation email sent to {$customer->name}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send service activation email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send welcome email to new customer
     */
    public function sendWelcomeEmail(Customer $customer): bool
    {
        try {
            if (!$customer->email) {
                return false;
            }

            $data = [
                'customer' => $customer,
                'company_name' => config('app.name', 'ISP Provider'),
                'company_email' => config('mail.from.address'),
                'company_phone' => config('app.company_phone', ''),
            ];

            Mail::send('emails.welcome', $data, function ($message) use ($customer) {
                $message->to($customer->email, $customer->name)
                    ->subject('Selamat Datang di ' . config('app.name'));
            });

            Log::info("Welcome email sent to {$customer->name}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send welcome email: " . $e->getMessage());
            return false;
        }
    }
}
