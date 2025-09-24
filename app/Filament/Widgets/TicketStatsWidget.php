<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class TicketStatsWidget extends BaseWidget
{
    protected static bool $isDiscovered = false; // Nonaktifkan widget ini
    protected static ?string $pollingInterval = '30s';

    protected static ?int $sort = 7;

    protected function getStats(): array
    {
        // Basic ticket counts
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', 'open')->count();
        $inProgressTickets = Ticket::where('status', 'in_progress')->count();
        $resolvedToday = Ticket::where('status', 'resolved')
            ->whereDate('resolved_at', today())
            ->count();

        // Overdue tickets
        $overdueTickets = Ticket::where('sla_due_at', '<', now())
            ->whereNotIn('status', ['resolved', 'closed', 'cancelled'])
            ->count();

        // Escalated tickets
        $escalatedTickets = Ticket::where('is_escalated', true)
            ->whereNotIn('status', ['resolved', 'closed', 'cancelled'])
            ->count();

        // My assigned tickets
        $myTickets = Ticket::where('assigned_to', auth()->id())
            ->whereNotIn('status', ['resolved', 'closed', 'cancelled'])
            ->count();

        // Critical priority tickets
        $criticalTickets = Ticket::whereHas('priority', function ($query) {
            $query->where('level', '>=', 4);
        })
            ->whereNotIn('status', ['resolved', 'closed', 'cancelled'])
            ->count();

        // Average response time today (in hours)
        $avgResponseTime = Ticket::whereNotNull('first_response_at')
            ->whereDate('created_at', today())
            ->get()
            ->avg(function ($ticket) {
                return $ticket->created_at->diffInMinutes($ticket->first_response_at) / 60;
            });

        return [
            Stat::make('Ticket Buka', $openTickets)
                ->description('Ticket yang belum ditangani')
                ->descriptionIcon('heroicon-m-ticket')
                ->color($openTickets > 10 ? 'danger' : 'primary'),

            Stat::make('Dalam Proses', $inProgressTickets)
                ->description('Sedang dikerjakan')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('warning'),

            Stat::make('Terlambat SLA', $overdueTickets)
                ->description('Melewati batas waktu')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueTickets > 0 ? 'danger' : 'success'),

            Stat::make('Tereskalasi', $escalatedTickets)
                ->description('Membutuhkan perhatian khusus')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($escalatedTickets > 0 ? 'danger' : 'success'),

            Stat::make('Ticket Saya', $myTickets)
                ->description('Ditugaskan kepada saya')
                ->descriptionIcon('heroicon-m-user')
                ->color($myTickets > 5 ? 'warning' : 'info'),

            Stat::make('Prioritas Kritis', $criticalTickets)
                ->description('Membutuhkan penanganan segera')
                ->descriptionIcon('heroicon-m-fire')
                ->color($criticalTickets > 0 ? 'danger' : 'success'),

            Stat::make('Selesai Hari Ini', $resolvedToday)
                ->description('Ticket yang diselesaikan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Avg Response Time', $avgResponseTime ? round($avgResponseTime, 1) . 'h' : 'N/A')
                ->description('Rata-rata waktu respon hari ini')
                ->descriptionIcon('heroicon-m-clock')
                ->color($avgResponseTime && $avgResponseTime > 4 ? 'danger' : 'success'),
        ];
    }
}
