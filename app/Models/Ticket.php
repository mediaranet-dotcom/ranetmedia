<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'title',
        'description',
        'customer_id',
        'service_id',
        'category_id',
        'priority_id',
        'assigned_to',
        'created_by',
        'status',
        'contact_method',
        'contact_value',
        'technical_details',
        'location',
        'requires_field_visit',
        'scheduled_visit_at',
        'sla_due_at',
        'escalation_due_at',
        'is_escalated',
        'escalation_level',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'resolution_notes',
        'customer_satisfaction_rating',
        'customer_feedback',
        'estimated_cost',
        'actual_cost',
        'is_billable',
        'is_warranty',
        'metadata',
        'total_comments',
        'total_attachments',
        'last_activity_at',
    ];

    protected $casts = [
        'technical_details' => 'array',
        'metadata' => 'array',
        'requires_field_visit' => 'boolean',
        'is_escalated' => 'boolean',
        'is_billable' => 'boolean',
        'is_warranty' => 'boolean',
        'scheduled_visit_at' => 'datetime',
        'sla_due_at' => 'datetime',
        'escalation_due_at' => 'datetime',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'customer_satisfaction_rating' => 'integer',
        'escalation_level' => 'integer',
        'total_comments' => 'integer',
        'total_attachments' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (!$ticket->ticket_number) {
                $ticket->ticket_number = static::generateTicketNumber();
            }
            
            $ticket->last_activity_at = now();
            
            // Set SLA due time based on priority
            if ($ticket->priority && !$ticket->sla_due_at) {
                $ticket->sla_due_at = $ticket->priority->getSlaDateTime($ticket->created_at ?? now());
            }
            
            // Set escalation due time
            if ($ticket->priority && $ticket->priority->escalation_hours && !$ticket->escalation_due_at) {
                $ticket->escalation_due_at = $ticket->priority->getEscalationDateTime($ticket->created_at ?? now());
            }
        });

        static::updating(function ($ticket) {
            $ticket->last_activity_at = now();
            
            // Set first response time
            if (!$ticket->first_response_at && $ticket->isDirty('status') && $ticket->status !== 'open') {
                $ticket->first_response_at = now();
            }
            
            // Set resolved time
            if (!$ticket->resolved_at && $ticket->isDirty('status') && $ticket->status === 'resolved') {
                $ticket->resolved_at = now();
            }
            
            // Set closed time
            if (!$ticket->closed_at && $ticket->isDirty('status') && $ticket->status === 'closed') {
                $ticket->closed_at = now();
            }
        });
    }

    /**
     * Generate unique ticket number
     */
    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        $lastTicket = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastTicket ? (int) substr($lastTicket->ticket_number, -4) + 1 : 1;
        
        return sprintf('TKT-%s%s-%04d', $year, $month, $nextNumber);
    }

    /**
     * Relationships
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'priority_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function slaTracking(): HasOne
    {
        return $this->hasOne(TicketSlaTracking::class);
    }

    /**
     * Scopes
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('sla_due_at', '<', now())
            ->whereNotIn('status', ['resolved', 'closed', 'cancelled']);
    }

    public function scopeEscalated($query)
    {
        return $query->where('is_escalated', true);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByPriority($query, $priorityLevel)
    {
        return $query->whereHas('priority', function ($q) use ($priorityLevel) {
            $q->where('level', $priorityLevel);
        });
    }

    /**
     * Helper methods
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isOverdue(): bool
    {
        return $this->sla_due_at && $this->sla_due_at->isPast() && !in_array($this->status, ['resolved', 'closed', 'cancelled']);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'info',
            'in_progress' => 'warning',
            'pending_customer' => 'secondary',
            'pending_vendor' => 'secondary',
            'resolved' => 'success',
            'closed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Buka',
            'in_progress' => 'Dalam Proses',
            'pending_customer' => 'Menunggu Customer',
            'pending_vendor' => 'Menunggu Vendor',
            'resolved' => 'Selesai',
            'closed' => 'Ditutup',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getResponseTimeAttribute(): ?int
    {
        if (!$this->first_response_at) {
            return null;
        }
        
        return $this->created_at->diffInMinutes($this->first_response_at);
    }

    public function getResolutionTimeAttribute(): ?int
    {
        if (!$this->resolved_at) {
            return null;
        }
        
        return $this->created_at->diffInMinutes($this->resolved_at);
    }

    public function getTimeToSlaBreachAttribute(): ?int
    {
        if (!$this->sla_due_at || $this->isResolved() || $this->isClosed()) {
            return null;
        }
        
        return now()->diffInMinutes($this->sla_due_at, false);
    }
}
