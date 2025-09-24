<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketPriority extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'level',
        'color',
        'icon',
        'sla_hours',
        'escalation_hours',
        'requires_immediate_notification',
        'send_whatsapp_notification',
        'send_email_notification',
        'is_active',
    ];

    protected $casts = [
        'level' => 'integer',
        'sla_hours' => 'integer',
        'escalation_hours' => 'integer',
        'requires_immediate_notification' => 'boolean',
        'send_whatsapp_notification' => 'boolean',
        'send_email_notification' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get tickets with this priority
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'priority_id');
    }

    /**
     * Get active tickets with this priority
     */
    public function activeTickets(): HasMany
    {
        return $this->tickets()->whereNotIn('status', ['closed', 'cancelled']);
    }

    /**
     * Get overdue tickets count
     */
    public function getOverdueTicketsCountAttribute(): int
    {
        return $this->tickets()
            ->where('sla_due_at', '<', now())
            ->whereNotIn('status', ['resolved', 'closed', 'cancelled'])
            ->count();
    }

    /**
     * Get escalated tickets count
     */
    public function getEscalatedTicketsCountAttribute(): int
    {
        return $this->tickets()
            ->where('is_escalated', true)
            ->whereNotIn('status', ['resolved', 'closed', 'cancelled'])
            ->count();
    }

    /**
     * Scope for active priorities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered by level
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('level');
    }

    /**
     * Check if this is a critical priority
     */
    public function isCritical(): bool
    {
        return $this->level >= 4;
    }

    /**
     * Check if this is a high priority
     */
    public function isHigh(): bool
    {
        return $this->level >= 3;
    }

    /**
     * Get priority badge color for UI
     */
    public function getBadgeColorAttribute(): string
    {
        return match ($this->level) {
            1 => 'success',
            2 => 'warning',
            3 => 'danger',
            4 => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get SLA due time from creation
     */
    public function getSlaDateTime(\DateTime $createdAt): \DateTime
    {
        return (clone $createdAt)->modify("+{$this->sla_hours} hours");
    }

    /**
     * Get escalation due time from creation
     */
    public function getEscalationDateTime(\DateTime $createdAt): ?\DateTime
    {
        if (!$this->escalation_hours) {
            return null;
        }
        
        return (clone $createdAt)->modify("+{$this->escalation_hours} hours");
    }
}
