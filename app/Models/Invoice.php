<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'service_id',
        'invoice_date',
        'due_date',
        'billing_period_start',
        'billing_period_end',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'outstanding_amount',
        'status',
        'notes',
        'metadata',
        'sent_at',
        'paid_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Helper methods
    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'sent' => 'info',
            'paid' => 'success',
            'partial_paid' => 'warning',
            'overdue' => 'danger',
            'cancelled' => 'secondary',
            default => 'gray',
        };
    }

    public function isOverdue(): bool
    {
        return $this->due_date < now() && $this->status !== 'paid';
    }

    public function getTaxRateLabel(): string
    {
        if ($this->tax_rate == 0) {
            return 'Tanpa PPN';
        }

        return 'PPN ' . number_format($this->tax_rate * 100, 0) . '%';
    }

    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return $this->due_date->diffInDays(now());
    }

    public function getPaymentProgress(): float
    {
        if ($this->total_amount == 0) {
            return 0;
        }
        return ($this->paid_amount / $this->total_amount) * 100;
    }

    // Auto-generate invoice number
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $yearMonth = now()->format('Ym');

        $lastInvoice = static::where('invoice_number', 'like', "{$prefix}-{$yearMonth}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $yearMonth, $nextNumber);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }

            // Ensure required fields have default values
            $invoice->subtotal = $invoice->subtotal ?? 0;
            $invoice->tax_rate = $invoice->tax_rate ?? 0;
            $invoice->tax_amount = $invoice->tax_amount ?? 0;
            $invoice->discount_amount = $invoice->discount_amount ?? 0;
            $invoice->total_amount = $invoice->total_amount ?? 0;
            $invoice->paid_amount = $invoice->paid_amount ?? 0;

            // Calculate outstanding amount
            $invoice->outstanding_amount = $invoice->total_amount - $invoice->paid_amount;
        });

        static::updating(function ($invoice) {
            // Ensure required fields have default values
            $invoice->subtotal = $invoice->subtotal ?? 0;
            $invoice->tax_rate = $invoice->tax_rate ?? 0;
            $invoice->tax_amount = $invoice->tax_amount ?? 0;
            $invoice->discount_amount = $invoice->discount_amount ?? 0;
            $invoice->total_amount = $invoice->total_amount ?? 0;
            $invoice->paid_amount = $invoice->paid_amount ?? 0;

            // Calculate outstanding amount
            $invoice->outstanding_amount = $invoice->total_amount - $invoice->paid_amount;
        });

        static::updating(function ($invoice) {
            // Recalculate outstanding amount
            $invoice->outstanding_amount = $invoice->total_amount - $invoice->paid_amount;

            // Update status based on payment
            if ($invoice->paid_amount >= $invoice->total_amount) {
                $invoice->status = 'paid';
                if (!$invoice->paid_at) {
                    $invoice->paid_at = now();
                }
            } elseif ($invoice->paid_amount > 0) {
                $invoice->status = 'partial_paid';
            } elseif ($invoice->isOverdue()) {
                $invoice->status = 'overdue';
            }
        });
    }
}
