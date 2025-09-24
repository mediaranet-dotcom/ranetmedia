<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'comment_id',
        'uploaded_by',
        'original_name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'file_hash',
        'type',
        'is_public',
        'description',
        'image_width',
        'image_height',
        'thumbnail_path',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_public' => 'boolean',
        'image_width' => 'integer',
        'image_height' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($attachment) {
            // Update ticket's total attachments count
            $attachment->ticket->increment('total_attachments');
        });

        static::deleted(function ($attachment) {
            // Update ticket's total attachments count
            $attachment->ticket->decrement('total_attachments');
            
            // Delete physical file
            if (Storage::exists($attachment->file_path)) {
                Storage::delete($attachment->file_path);
            }
            
            // Delete thumbnail if exists
            if ($attachment->thumbnail_path && Storage::exists($attachment->thumbnail_path)) {
                Storage::delete($attachment->thumbnail_path);
            }
        });
    }

    /**
     * Relationships
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(TicketComment::class, 'comment_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scopes
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeDocuments($query)
    {
        return $query->where('type', 'document');
    }

    /**
     * Helper methods
     */
    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isDocument(): bool
    {
        return $this->type === 'document';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isAudio(): bool
    {
        return $this->type === 'audio';
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return null;
        }
        
        return Storage::url($this->thumbnail_path);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getFileExtensionAttribute(): string
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    public function getFileIconAttribute(): string
    {
        $extension = strtolower($this->file_extension);
        
        return match ($extension) {
            'pdf' => 'heroicon-o-document-text',
            'doc', 'docx' => 'heroicon-o-document',
            'xls', 'xlsx' => 'heroicon-o-table-cells',
            'ppt', 'pptx' => 'heroicon-o-presentation-chart-bar',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'heroicon-o-photo',
            'mp4', 'avi', 'mov', 'wmv' => 'heroicon-o-video-camera',
            'mp3', 'wav', 'ogg' => 'heroicon-o-musical-note',
            'zip', 'rar', '7z' => 'heroicon-o-archive-box',
            'txt' => 'heroicon-o-document-text',
            default => 'heroicon-o-document',
        };
    }

    /**
     * Determine file type from mime type
     */
    public static function determineFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } elseif (in_array($mimeType, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
        ])) {
            return 'document';
        } else {
            return 'other';
        }
    }
}
