<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Services\WhatsAppService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class WhatsAppStatsWidget extends BaseWidget
{
    protected static bool $isDiscovered = true; // Aktifkan widget WhatsApp
    protected static ?string $pollingInterval = '30s';

    protected static ?int $sort = 6;

    protected function getStats(): array
    {
        $whatsappService = app(WhatsAppService::class);
        $usageStats = $whatsappService->getUsageStats();

        // Count customers with phone numbers
        $customersWithPhone = Customer::whereNotNull('phone')->count();
        $totalCustomers = Customer::count();
        $phonePercentage = $totalCustomers > 0 ? round(($customersWithPhone / $totalCustomers) * 100) : 0;

        // Calculate total daily usage across all accounts
        $totalDailyUsage = 0;
        $totalDailyLimit = 0;
        $availableAccounts = 0;

        foreach ($usageStats as $accountName => $stat) {
            $totalDailyUsage += $stat['daily_usage'];
            $totalDailyLimit += $stat['daily_limit'];
            if ($stat['available']) {
                $availableAccounts++;
            }
        }

        // Get today's WhatsApp activity from cache
        $todayKey = "whatsapp_activity_" . now()->format('Y-m-d');
        $todayActivity = Cache::get($todayKey, [
            'invoices_sent' => 0,
            'reminders_sent' => 0,
            'failed_attempts' => 0
        ]);

        return [
            Stat::make('Customer dengan WhatsApp', $customersWithPhone)
                ->description("{$phonePercentage}% dari total customer")
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('success'),

            Stat::make('Pesan Hari Ini', $totalDailyUsage)
                ->description("dari {$totalDailyLimit} limit harian")
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($totalDailyUsage > ($totalDailyLimit * 0.8) ? 'warning' : 'primary'),

            Stat::make('Akun WhatsApp Aktif', $availableAccounts)
                ->description("dari " . count($usageStats) . " total akun")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($availableAccounts > 0 ? 'success' : 'danger'),

            Stat::make('Invoice Terkirim', $todayActivity['invoices_sent'])
                ->description("via WhatsApp hari ini")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Pengingat Terkirim', $todayActivity['reminders_sent'])
                ->description("via WhatsApp hari ini")
                ->descriptionIcon('heroicon-m-bell')
                ->color('warning'),
        ];
    }
}
