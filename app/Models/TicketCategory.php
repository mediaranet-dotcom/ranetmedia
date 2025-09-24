<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'default_priority_level',
        'default_sla_hours',
        'requires_technical_team',
        'auto_assign_to_department',
        'department',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'requires_technical_team' => 'boolean',
        'auto_assign_to_department' => 'boolean',
        'is_active' => 'boolean',
        'default_priority_level' => 'integer',
        'default_sla_hours' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get tickets in this category
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    /**
     * Get active tickets in this category
     */
    public function activeTickets(): HasMany
    {
        return $this->tickets()->whereNotIn('status', ['closed', 'cancelled']);
    }

    /**
     * Get open tickets count
     */
    public function getOpenTicketsCountAttribute(): int
    {
        return $this->tickets()->where('status', 'open')->count();
    }

    /**
     * Get in progress tickets count
     */
    public function getInProgressTicketsCountAttribute(): int
    {
        return $this->tickets()->where('status', 'in_progress')->count();
    }

    /**
     * Get total tickets count
     */
    public function getTotalTicketsCountAttribute(): int
    {
        return $this->tickets()->count();
    }

    /**
     * Get average resolution time in hours
     */
    public function getAverageResolutionTimeAttribute(): ?float
    {
        $resolvedTickets = $this->tickets()
            ->whereNotNull('resolved_at')
            ->get();

        if ($resolvedTickets->isEmpty()) {
            return null;
        }

        $totalMinutes = $resolvedTickets->sum(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->resolved_at);
        });

        return round($totalMinutes / $resolvedTickets->count() / 60, 2);
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get department color
     */
    public function getDepartmentColorAttribute(): string
    {
        return match ($this->department) {
            'technical' => '#EF4444',
            'billing' => '#F59E0B',
            'sales' => '#10B981',
            'support' => '#3B82F6',
            default => '#6B7280',
        };
    }

    /**
     * Get department icon
     */
    public function getDepartmentIconAttribute(): string
    {
        return match ($this->department) {
            'technical' => 'heroicon-o-wrench-screwdriver',
            'billing' => 'heroicon-o-banknotes',
            'sales' => 'heroicon-o-chart-bar',
            'support' => 'heroicon-o-chat-bubble-left-right',
            default => 'heroicon-o-building-office',
        };
    }
}
