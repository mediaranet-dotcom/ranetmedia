<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WhatsAppService
{
    protected array $whatsappAccounts;
    protected int $currentAccountIndex = 0;
    protected int $dailyLimit = 200; // Per account per day
    protected int $hourlyLimit = 20; // Per account per hour

    public function __construct()
    {
        // Multiple WhatsApp accounts untuk load balancing
        $this->whatsappAccounts = [
            [
                'name' => 'WA-1',
                'phone' => '6281234567890',
                'api_url' => env('WHATSAPP_API_URL_1', 'http://localhost:3001'),
                'session' => 'session1',
            ],
            [
                'name' => 'WA-2',
                'phone' => '6281234567891',
                'api_url' => env('WHATSAPP_API_URL_2', 'http://localhost:3002'),
                'session' => 'session2',
            ],
            [
                'name' => 'WA-3',
                'phone' => '6281234567892',
                'api_url' => env('WHATSAPP_API_URL_3', 'http://localhost:3003'),
                'session' => 'session3',
            ],
        ];
    }

    /**
     * Send invoice notification via WhatsApp
     */
    public function sendInvoiceNotification(Invoice $invoice): bool
    {
        try {
            $customer = $invoice->customer;

            if (!$this->isValidPhoneNumber($customer->phone)) {
                Log::warning("Invalid phone number for customer {$customer->name}: {$customer->phone}");
                return false;
            }

            $message = $this->generateInvoiceMessage($invoice);

            return $this->sendMessage($customer->phone, $message, 'invoice');
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp invoice notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send payment reminder via WhatsApp
     */
    public function sendPaymentReminder(Invoice $invoice): bool
    {
        try {
            $customer = $invoice->customer;

            if (!$this->isValidPhoneNumber($customer->phone)) {
                return false;
            }

            $daysOverdue = $invoice->getDaysOverdue();
            $urgencyLevel = $this->getUrgencyLevel($daysOverdue);

            $message = $this->generateReminderMessage($invoice, $urgencyLevel, $daysOverdue);

            return $this->sendMessage($customer->phone, $message, 'reminder', $urgencyLevel);
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp payment reminder: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk notifications with smart scheduling
     */
    public function sendBulkNotifications(array $invoices, string $type = 'invoice'): array
    {
        $results = [
            'sent' => 0,
            'failed' => 0,
            'queued' => 0,
            'skipped' => 0,
            'details' => []
        ];

        // Sort by priority
        $prioritizedInvoices = $this->prioritizeInvoices($invoices);

        foreach ($prioritizedInvoices as $invoice) {
            // Check rate limits
            if (!$this->canSendMessage()) {
                // Queue for later
                $this->queueMessage($invoice, $type);
                $results['queued']++;
                continue;
            }

            try {
                $success = $type === 'reminder'
                    ? $this->sendPaymentReminder($invoice)
                    : $this->sendInvoiceNotification($invoice);

                if ($success) {
                    $results['sent']++;
                    $results['details'][] = [
                        'invoice' => $invoice->invoice_number,
                        'customer' => $invoice->customer->name,
                        'status' => 'sent'
                    ];

                    // Add delay to avoid rate limiting
                    sleep(2);
                } else {
                    $results['failed']++;
                    $results['details'][] = [
                        'invoice' => $invoice->invoice_number,
                        'customer' => $invoice->customer->name,
                        'status' => 'failed'
                    ];
                }
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

        Log::info("WhatsApp bulk notifications completed", $results);
        return $results;
    }

    /**
     * Send message with load balancing
     */
    protected function sendMessage(string $phone, string $message, string $type = 'general', string $priority = 'medium'): bool
    {
        // Check if in test mode
        if (env('WHATSAPP_TEST_MODE', false)) {
            return $this->sendTestMessage($phone, $message, $type);
        }

        $account = $this->getAvailableAccount();

        if (!$account) {
            Log::warning("No available WhatsApp account for sending message");
            return false;
        }

        try {
            // Format phone number
            $formattedPhone = $this->formatPhoneNumber($phone);

            // Send via WhatsApp Web API (using wppconnect or similar)
            $response = Http::timeout(30)->post($account['api_url'] . '/api/sendText', [
                'session' => $account['session'],
                'number' => $formattedPhone,
                'text' => $message
            ]);

            if ($response->successful()) {
                $this->incrementUsageCount($account['name']);
                Log::info("WhatsApp message sent successfully via {$account['name']} to {$phone}");
                return true;
            } else {
                Log::error("WhatsApp API error via {$account['name']}: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp send error via {$account['name']}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send test message (simulation mode)
     */
    protected function sendTestMessage(string $phone, string $message, string $type): bool
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phone);

            // Log the message that would be sent
            Log::info("TEST MODE - WhatsApp message would be sent to {$formattedPhone}:");
            Log::info("Message: " . $message);

            // Create WhatsApp URL for manual testing
            $whatsappUrl = "https://wa.me/{$formattedPhone}?text=" . urlencode($message);
            Log::info("WhatsApp URL: " . $whatsappUrl);

            // Store in cache for dashboard display
            $testMessages = \Cache::get('whatsapp_test_messages', []);
            $testMessages[] = [
                'phone' => $formattedPhone,
                'message' => $message,
                'type' => $type,
                'url' => $whatsappUrl,
                'sent_at' => now()->toISOString()
            ];

            // Keep only last 10 messages
            if (count($testMessages) > 10) {
                $testMessages = array_slice($testMessages, -10);
            }

            \Cache::put('whatsapp_test_messages', $testMessages, now()->addHours(24));

            return true;
        } catch (\Exception $e) {
            Log::error("WhatsApp test mode error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get available WhatsApp account with load balancing
     */
    protected function getAvailableAccount(): ?array
    {
        foreach ($this->whatsappAccounts as $account) {
            if ($this->isAccountAvailable($account['name'])) {
                return $account;
            }
        }

        // If all accounts are at limit, return the one with lowest usage
        return $this->getLeastUsedAccount();
    }

    /**
     * Check if account is available (not at rate limit)
     */
    protected function isAccountAvailable(string $accountName): bool
    {
        $hourlyKey = "whatsapp_hourly_{$accountName}_" . now()->format('Y-m-d-H');
        $dailyKey = "whatsapp_daily_{$accountName}_" . now()->format('Y-m-d');

        $hourlyCount = Cache::get($hourlyKey, 0);
        $dailyCount = Cache::get($dailyKey, 0);

        return $hourlyCount < $this->hourlyLimit && $dailyCount < $this->dailyLimit;
    }

    /**
     * Increment usage count for rate limiting
     */
    protected function incrementUsageCount(string $accountName): void
    {
        $hourlyKey = "whatsapp_hourly_{$accountName}_" . now()->format('Y-m-d-H');
        $dailyKey = "whatsapp_daily_{$accountName}_" . now()->format('Y-m-d');

        Cache::increment($hourlyKey);
        Cache::increment($dailyKey);

        // Set expiry
        Cache::put($hourlyKey, Cache::get($hourlyKey), now()->addHour());
        Cache::put($dailyKey, Cache::get($dailyKey), now()->addDay());
    }

    /**
     * Get least used account
     */
    protected function getLeastUsedAccount(): array
    {
        $leastUsed = $this->whatsappAccounts[0];
        $minUsage = PHP_INT_MAX;

        foreach ($this->whatsappAccounts as $account) {
            $dailyKey = "whatsapp_daily_{$account['name']}_" . now()->format('Y-m-d');
            $usage = Cache::get($dailyKey, 0);

            if ($usage < $minUsage) {
                $minUsage = $usage;
                $leastUsed = $account;
            }
        }

        return $leastUsed;
    }

    /**
     * Check if can send message (global rate limiting)
     */
    protected function canSendMessage(): bool
    {
        foreach ($this->whatsappAccounts as $account) {
            if ($this->isAccountAvailable($account['name'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Queue message for later sending
     */
    protected function queueMessage(Invoice $invoice, string $type): void
    {
        // Simple queue implementation using cache
        $queueKey = "whatsapp_queue_" . now()->format('Y-m-d-H');
        $queue = Cache::get($queueKey, []);

        $queue[] = [
            'invoice_id' => $invoice->id,
            'type' => $type,
            'queued_at' => now()->toISOString()
        ];

        Cache::put($queueKey, $queue, now()->addHours(2));
    }

    /**
     * Prioritize invoices for sending
     */
    protected function prioritizeInvoices(array $invoices): array
    {
        return collect($invoices)->sortBy(function ($invoice) {
            // Priority: overdue > due today > future
            if ($invoice->isOverdue()) {
                return 1; // Highest priority
            } elseif ($invoice->due_date->isToday()) {
                return 2; // Medium priority
            } else {
                return 3; // Low priority
            }
        })->values()->toArray();
    }

    /**
     * Generate invoice message
     */
    protected function generateInvoiceMessage(Invoice $invoice): string
    {
        $customer = $invoice->customer;
        $service = $invoice->service;
        $package = $service->package;

        // Get company settings
        $company = CompanySetting::getActive();

        return "🧾 *TAGIHAN INTERNET*\n\n" .
            "Halo *{$customer->name}*,\n\n" .
            "📋 Invoice: *{$invoice->invoice_number}*\n" .
            "📦 Paket: {$package->name}\n" .
            "💰 Total: *Rp " . number_format($invoice->total_amount, 0, ',', '.') . "*\n" .
            "📅 Jatuh Tempo: *{$invoice->due_date->format('d/m/Y')}*\n\n" .
            "💳 *PEMBAYARAN:*\n" .
            $this->getBankDetailsForMessage($company) .
            "📞 Konfirmasi: {$company->company_phone}\n\n" .
            "Terima kasih! 🙏";
    }

    /**
     * Get formatted bank details for WhatsApp message
     */
    protected function getBankDetailsForMessage(CompanySetting $company): string
    {
        $bankDetails = $company->bank_details ?? "Bank BCA: 1234567890\nBank Mandiri: 0987654321\nAtas Nama: {$company->company_name}";

        // Parse bank details and format for WhatsApp
        $lines = explode("\n", $bankDetails);
        $formatted = "";

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Format each line with bullet point
            if (strpos($line, 'Bank') === 0 || strpos($line, 'Atas Nama') === 0 || strpos($line, 'a.n.') === 0) {
                $formatted .= "• {$line}\n";
            } elseif (strpos($line, 'E-Wallet') === 0) {
                $formatted .= "\n💰 *E-WALLET:*\n";
            } elseif (strpos($line, 'GoPay') === 0 || strpos($line, 'OVO') === 0 || strpos($line, 'DANA') === 0) {
                $formatted .= "• {$line}\n";
            } else {
                $formatted .= "• {$line}\n";
            }
        }

        return $formatted . "\n";
    }

    /**
     * Generate reminder message based on urgency
     */
    protected function generateReminderMessage(Invoice $invoice, string $urgencyLevel, int $daysOverdue): string
    {
        $customer = $invoice->customer;

        $urgencyEmoji = match ($urgencyLevel) {
            'final' => '🚨',
            'urgent' => '⚠️',
            'reminder' => '📢',
            default => '💌'
        };

        $urgencyText = match ($urgencyLevel) {
            'final' => '*PERINGATAN TERAKHIR*',
            'urgent' => '*PEMBAYARAN URGENT*',
            'reminder' => '*PENGINGAT PEMBAYARAN*',
            default => '*PENGINGAT TAGIHAN*'
        };

        // Get company settings
        $company = CompanySetting::getActive();

        return "{$urgencyEmoji} {$urgencyText}\n\n" .
            "Halo *{$customer->name}*,\n\n" .
            "📋 Invoice: *{$invoice->invoice_number}*\n" .
            "💰 Total: *Rp " . number_format($invoice->outstanding_amount, 0, ',', '.') . "*\n" .
            "⏰ Terlambat: *{$daysOverdue} hari*\n\n" .
            ($urgencyLevel === 'final' ?
                "🚨 *LAYANAN AKAN DIPUTUS DALAM 24 JAM!*\n\n" :
                "Mohon segera lakukan pembayaran.\n\n") .
            "💳 *PEMBAYARAN:*\n" .
            $this->getBankDetailsForMessage($company) .
            "📞 Bantuan: {$company->company_phone}\n\n" .
            "Terima kasih! 🙏";
    }

    /**
     * Get urgency level based on days overdue
     */
    protected function getUrgencyLevel(int $daysOverdue): string
    {
        return match (true) {
            $daysOverdue <= 3 => 'gentle',
            $daysOverdue <= 7 => 'reminder',
            $daysOverdue <= 14 => 'urgent',
            default => 'final'
        };
    }

    /**
     * Validate phone number
     */
    protected function isValidPhoneNumber(?string $phone): bool
    {
        if (!$phone) return false;

        // Remove all non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Check if it's a valid Indonesian number
        return strlen($cleaned) >= 10 && strlen($cleaned) <= 15;
    }

    /**
     * Format phone number for WhatsApp
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Add country code if not present
        if (substr($cleaned, 0, 2) !== '62') {
            if (substr($cleaned, 0, 1) === '0') {
                $cleaned = '62' . substr($cleaned, 1);
            } else {
                $cleaned = '62' . $cleaned;
            }
        }

        return $cleaned;
    }

    /**
     * Get usage statistics
     */
    public function getUsageStats(): array
    {
        $stats = [];

        foreach ($this->whatsappAccounts as $account) {
            $hourlyKey = "whatsapp_hourly_{$account['name']}_" . now()->format('Y-m-d-H');
            $dailyKey = "whatsapp_daily_{$account['name']}_" . now()->format('Y-m-d');

            $stats[$account['name']] = [
                'hourly_usage' => Cache::get($hourlyKey, 0),
                'daily_usage' => Cache::get($dailyKey, 0),
                'hourly_limit' => $this->hourlyLimit,
                'daily_limit' => $this->dailyLimit,
                'available' => $this->isAccountAvailable($account['name'])
            ];
        }

        return $stats;
    }
}
