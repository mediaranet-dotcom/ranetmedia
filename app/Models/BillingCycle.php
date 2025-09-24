<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class BillingCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'interval_count',
        'interval_type',
        'billing_day',
        'due_days',
        'is_active',
        'description',
    ];

    protected $casts = [
        'interval_count' => 'integer',
        'billing_day' => 'integer',
        'due_days' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    // Helper methods
    public function getIntervalLabel(): string
    {
        $type = match ($this->interval_type) {
            'day' => 'hari',
            'week' => 'minggu',
            'month' => 'bulan',
            'year' => 'tahun',
            default => $this->interval_type,
        };

        return $this->interval_count . ' ' . $type;
    }

    public function calculateNextBillingDate(Carbon $fromDate = null): Carbon
    {
        $fromDate = $fromDate ?? now();

        return match ($this->interval_type) {
            'day' => $fromDate->addDays($this->interval_count),
            'week' => $fromDate->addWeeks($this->interval_count),
            'month' => $fromDate->addMonths($this->interval_count)->day($this->billing_day),
            'year' => $fromDate->addYears($this->interval_count)->day($this->billing_day),
            default => $fromDate->addMonths($this->interval_count),
        };
    }

    public function calculateDueDate(Carbon $invoiceDate): Carbon
    {
        return $invoiceDate->copy()->addDays($this->due_days);
    }

    // Scope for active cycles
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
