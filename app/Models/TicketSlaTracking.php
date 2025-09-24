<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TicketSlaTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'sla_start_time',
        'sla_due_time',
        'first_response_time',
        'resolution_time',
        'response_time_minutes',
        'resolution_time_minutes',
        'total_business_hours',
        'total_calendar_hours',
        'response_sla_met',
        'resolution_sla_met',
        'response_sla_breach_minutes',
        'resolution_sla_breach_minutes',
        'sla_paused_at',
        'sla_resumed_at',
        'total_paused_minutes',
        'pause_reason',
        'business_hours_config',
        'was_escalated',
        'escalated_at',
        'escalation_level',
        'escalation_reason',
    ];

    protected $casts = [
        'sla_start_time' => 'datetime',
        'sla_due_time' => 'datetime',
        'first_response_time' => 'datetime',
        'resolution_time' => 'datetime',
        'response_time_minutes' => 'integer',
        'resolution_time_minutes' => 'integer',
        'total_business_hours' => 'integer',
        'total_calendar_hours' => 'integer',
        'response_sla_met' => 'boolean',
        'resolution_sla_met' => 'boolean',
        'response_sla_breach_minutes' => 'integer',
        'resolution_sla_breach_minutes' => 'integer',
        'sla_paused_at' => 'datetime',
        'sla_resumed_at' => 'datetime',
        'total_paused_minutes' => 'integer',
        'business_hours_config' => 'array',
        'was_escalated' => 'boolean',
        'escalated_at' => 'datetime',
        'escalation_level' => 'integer',
    ];

    /**
     * Relationships
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Calculate response time when first response is given
     */
    public function calculateResponseTime(): void
    {
        if (!$this->first_response_time) {
            return;
        }

        $this->response_time_minutes = $this->sla_start_time->diffInMinutes($this->first_response_time);
        $this->response_sla_met = $this->first_response_time <= $this->sla_due_time;
        
        if (!$this->response_sla_met) {
            $this->response_sla_breach_minutes = $this->sla_due_time->diffInMinutes($this->first_response_time);
        }

        $this->save();
    }

    /**
     * Calculate resolution time when ticket is resolved
     */
    public function calculateResolutionTime(): void
    {
        if (!$this->resolution_time) {
            return;
        }

        $this->resolution_time_minutes = $this->sla_start_time->diffInMinutes($this->resolution_time);
        $this->total_calendar_hours = intval($this->resolution_time_minutes / 60);
        
        // Calculate business hours (simplified - assumes 8 hours per day, Mon-Fri)
        $this->total_business_hours = $this->calculateBusinessHours(
            $this->sla_start_time,
            $this->resolution_time
        );

        // Check if resolution SLA was met
        $this->resolution_sla_met = $this->resolution_time <= $this->sla_due_time;
        
        if (!$this->resolution_sla_met) {
            $this->resolution_sla_breach_minutes = $this->sla_due_time->diffInMinutes($this->resolution_time);
        }

        $this->save();
    }

    /**
     * Pause SLA timer (e.g., when waiting for customer response)
     */
    public function pauseSla(string $reason = null): void
    {
        if ($this->sla_paused_at) {
            return; // Already paused
        }

        $this->sla_paused_at = now();
        $this->pause_reason = $reason;
        $this->save();
    }

    /**
     * Resume SLA timer
     */
    public function resumeSla(): void
    {
        if (!$this->sla_paused_at) {
            return; // Not paused
        }

        $pausedMinutes = $this->sla_paused_at->diffInMinutes(now());
        $this->total_paused_minutes += $pausedMinutes;
        
        // Extend SLA due time by the paused duration
        $this->sla_due_time = $this->sla_due_time->addMinutes($pausedMinutes);
        
        $this->sla_paused_at = null;
        $this->sla_resumed_at = now();
        $this->pause_reason = null;
        
        $this->save();
    }

    /**
     * Mark as escalated
     */
    public function markEscalated(int $level, string $reason = null): void
    {
        $this->was_escalated = true;
        $this->escalated_at = now();
        $this->escalation_level = $level;
        $this->escalation_reason = $reason;
        $this->save();
    }

    /**
     * Check if SLA is currently breached
     */
    public function isSlaBreached(): bool
    {
        if ($this->resolution_time) {
            return !$this->resolution_sla_met;
        }

        return now() > $this->sla_due_time;
    }

    /**
     * Get time remaining until SLA breach
     */
    public function getTimeToSlaBreachAttribute(): ?int
    {
        if ($this->resolution_time || $this->sla_paused_at) {
            return null;
        }

        return now()->diffInMinutes($this->sla_due_time, false);
    }

    /**
     * Get formatted time to SLA breach
     */
    public function getFormattedTimeToSlaBreachAttribute(): ?string
    {
        $minutes = $this->time_to_sla_breach;
        
        if ($minutes === null) {
            return null;
        }

        if ($minutes < 0) {
            $minutes = abs($minutes);
            $prefix = 'Terlambat ';
        } else {
            $prefix = 'Sisa ';
        }

        $hours = intval($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 24) {
            $days = intval($hours / 24);
            $remainingHours = $hours % 24;
            return $prefix . "{$days}d {$remainingHours}h";
        } elseif ($hours > 0) {
            return $prefix . "{$hours}h {$remainingMinutes}m";
        } else {
            return $prefix . "{$remainingMinutes}m";
        }
    }

    /**
     * Calculate business hours between two dates (simplified)
     */
    private function calculateBusinessHours(Carbon $start, Carbon $end): int
    {
        $businessHours = 0;
        $current = $start->copy();

        while ($current < $end) {
            // Skip weekends
            if ($current->isWeekend()) {
                $current->addDay()->startOfDay();
                continue;
            }

            // Calculate hours for this day
            $dayStart = $current->copy()->setTime(8, 0); // 8 AM
            $dayEnd = $current->copy()->setTime(17, 0);   // 5 PM

            $periodStart = $current > $dayStart ? $current : $dayStart;
            $periodEnd = $end < $dayEnd ? $end : $dayEnd;

            if ($periodStart < $periodEnd) {
                $businessHours += $periodStart->diffInHours($periodEnd);
            }

            $current->addDay()->startOfDay();
        }

        return $businessHours;
    }
}
