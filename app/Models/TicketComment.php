<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'author_name',
        'author_email',
        'author_type',
        'content',
        'type',
        'is_internal',
        'is_public',
        'notify_customer',
        'notify_assigned_staff',
        'old_value',
        'new_value',
        'metadata',
        'time_spent_minutes',
        'is_billable_time',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_public' => 'boolean',
        'notify_customer' => 'boolean',
        'notify_assigned_staff' => 'boolean',
        'metadata' => 'array',
        'time_spent_minutes' => 'integer',
        'is_billable_time' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($comment) {
            // Update ticket's total comments count
            $comment->ticket->increment('total_comments');
            $comment->ticket->update(['last_activity_at' => now()]);
        });

        static::deleted(function ($comment) {
            // Update ticket's total comments count
            $comment->ticket->decrement('total_comments');
        });
    }

    /**
     * Relationships
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class, 'comment_id');
    }

    /**
     * Scopes
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    public function scopeByAuthorType($query, $type)
    {
        return $query->where('author_type', $type);
    }

    public function scopeComments($query)
    {
        return $query->where('type', 'comment');
    }

    public function scopeSystemNotes($query)
    {
        return $query->where('type', 'system_note');
    }

    /**
     * Helper methods
     */
    public function isFromCustomer(): bool
    {
        return $this->author_type === 'customer';
    }

    public function isFromStaff(): bool
    {
        return $this->author_type === 'staff';
    }

    public function isSystemNote(): bool
    {
        return $this->author_type === 'system';
    }

    public function isStatusChange(): bool
    {
        return $this->type === 'status_change';
    }

    public function isAssignmentChange(): bool
    {
        return $this->type === 'assignment_change';
    }

    public function isPriorityChange(): bool
    {
        return $this->type === 'priority_change';
    }

    public function getAuthorDisplayNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        
        if ($this->author_name) {
            return $this->author_name;
        }
        
        return 'System';
    }

    public function getTypeDisplayNameAttribute(): string
    {
        return match ($this->type) {
            'comment' => 'Komentar',
            'status_change' => 'Perubahan Status',
            'assignment_change' => 'Perubahan Penugasan',
            'priority_change' => 'Perubahan Prioritas',
            'category_change' => 'Perubahan Kategori',
            'resolution' => 'Penyelesaian',
            'escalation' => 'Eskalasi',
            'system_note' => 'Catatan Sistem',
            default => $this->type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'comment' => 'primary',
            'status_change' => 'info',
            'assignment_change' => 'warning',
            'priority_change' => 'danger',
            'category_change' => 'secondary',
            'resolution' => 'success',
            'escalation' => 'danger',
            'system_note' => 'secondary',
            default => 'secondary',
        };
    }

    public function getFormattedTimeSpentAttribute(): ?string
    {
        if (!$this->time_spent_minutes) {
            return null;
        }

        $hours = intval($this->time_spent_minutes / 60);
        $minutes = $this->time_spent_minutes % 60;

        if ($hours > 0) {
            return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours}h";
        }

        return "{$minutes}m";
    }
}
