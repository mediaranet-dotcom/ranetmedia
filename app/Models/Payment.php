<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'invoice_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'notes',
        'year',
        'month',
        'status',
        'transaction_id',
        'payment_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get customer attribute (accessor)
     */
    public function getCustomerAttribute()
    {
        return $this->service?->customer ?? $this->invoice?->customer;
    }

    public function getPaymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'e_wallet' => 'E-Wallet',
            'other' => 'Other',
            default => 'Unknown',
        };
    }

    protected $appends = ['year', 'month'];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Validate payment period before creating
        static::creating(function ($payment) {
            $payment->validatePaymentPeriod();
        });

        // Update invoice when payment is created
        static::created(function ($payment) {
            if ($payment->invoice_id && $payment->status === 'completed') {
                $payment->updateInvoiceStatus();
            }
        });

        // Validate payment period before updating (only if period fields changed)
        static::updating(function ($payment) {
            $original = $payment->getOriginal();

            // Only validate if service_id, month, or year changed
            if (
                $payment->service_id != $original['service_id'] ||
                $payment->month != $original['month'] ||
                $payment->year != $original['year']
            ) {
                $payment->validatePaymentPeriod();
            }
        });

        // Update invoice when payment is updated
        static::updated(function ($payment) {
            if ($payment->invoice_id) {
                $payment->updateInvoiceStatus();
            }
        });

        // Update invoice when payment is deleted
        static::deleted(function ($payment) {
            if ($payment->invoice_id) {
                $payment->updateInvoiceStatus();
            }
        });
    }

    // Getter untuk year
    public function getYearAttribute()
    {
        return $this->attributes['year'] ?? ($this->payment_date ? $this->payment_date->year : null);
    }

    // Getter untuk month
    public function getMonthAttribute()
    {
        return $this->attributes['month'] ?? ($this->payment_date ? $this->payment_date->month : null);
    }

    // Setter untuk year
    public function setYearAttribute($value)
    {
        $this->attributes['year'] = $value;
    }

    // Setter untuk month
    public function setMonthAttribute($value)
    {
        $this->attributes['month'] = $value;
    }

    // Getter untuk periode (format: Juli-2025)
    public function getPeriodeAttribute()
    {
        if (!$this->payment_date) {
            return null;
        }

        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $month = $this->payment_date->month;
        $year = $this->payment_date->year;

        return $monthNames[$month] . '-' . $year;
    }

    /**
     * Check if customer already has payment for specific month/year
     */
    public static function hasPaymentForPeriod($serviceId, $month, $year, $excludePaymentId = null)
    {
        $query = static::where('service_id', $serviceId)
            ->where('month', $month)
            ->where('year', $year);

        if ($excludePaymentId) {
            $query->where('id', '!=', $excludePaymentId);
        }

        return $query->exists();
    }

    /**
     * Get existing payment for specific period
     */
    public static function getPaymentForPeriod($serviceId, $month, $year)
    {
        return static::where('service_id', $serviceId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
    }

    /**
     * Validate payment period before saving
     */
    public function validatePaymentPeriod()
    {
        if (!$this->service_id || !$this->month || !$this->year) {
            return true; // Skip validation if required fields are missing
        }

        $existingPayment = static::hasPaymentForPeriod(
            $this->service_id,
            $this->month,
            $this->year,
            $this->id
        );

        if ($existingPayment) {
            $monthNames = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];

            $periodeName = $monthNames[$this->month] . ' ' . $this->year;
            throw new \Exception("Pembayaran untuk periode {$periodeName} sudah ada. Tidak dapat membuat pembayaran ganda dalam periode yang sama.");
        }

        return true;
    }

    /**
     * Update invoice status based on payments
     */
    public function updateInvoiceStatus()
    {
        if (!$this->invoice_id) {
            return;
        }

        $invoice = $this->invoice;
        if (!$invoice) {
            return;
        }

        // Calculate total paid amount from all completed payments
        $totalPaid = $invoice->payments()
            ->where('status', 'completed')
            ->sum('amount');

        // Update invoice paid amount
        $invoice->paid_amount = $totalPaid;
        $invoice->outstanding_amount = $invoice->total_amount - $totalPaid;

        // Update invoice status
        if ($totalPaid >= $invoice->total_amount) {
            $invoice->status = 'paid';
            if (!$invoice->paid_at) {
                $invoice->paid_at = now();
            }
        } elseif ($totalPaid > 0) {
            $invoice->status = 'partial_paid';
        } elseif ($invoice->due_date < now()) {
            $invoice->status = 'overdue';
        } else {
            $invoice->status = 'sent';
        }

        $invoice->save();
    }
}
