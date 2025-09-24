<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'type',
        'quantity',
        'unit_price',
        'total_price',
        'service_period_start',
        'service_period_end',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'service_period_start' => 'date',
        'service_period_end' => 'date',
        'metadata' => 'array',
    ];

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // Helper methods
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'subscription' => 'Langganan',
            'installation' => 'Instalasi',
            'equipment' => 'Peralatan',
            'penalty' => 'Denda',
            'discount' => 'Diskon',
            'other' => 'Lainnya',
            default => ucfirst($this->type),
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            // Auto-calculate total price
            $item->total_price = $item->quantity * $item->unit_price;
        });

        static::updating(function ($item) {
            // Auto-calculate total price
            $item->total_price = $item->quantity * $item->unit_price;
        });
    }
}
