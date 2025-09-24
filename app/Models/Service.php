<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'package_id',
        'network_type',
        'odp_id',
        'odp_port',
        'fiber_cable_color',
        'signal_strength',
        'wireless_equipment',
        'antenna_type',
        'frequency',
        'htb_server',
        'access_point',
        'installation_notes',
        'ip_address',
        'router_name',
        'start_date',
        'end_date',
        'status',
        'auto_billing',
        'next_billing_date',
        'last_billed_date',
        'billing_cycle_id',
        'billing_day',
        'monthly_fee'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_date' => 'date',
        'last_billed_date' => 'date',
        'status' => 'string',
        'network_type' => 'string',
        'auto_billing' => 'boolean',
        'billing_day' => 'integer',
        'monthly_fee' => 'decimal:2',
        'odp_port' => 'integer',
        'signal_strength' => 'decimal:2',
        'frequency' => 'decimal:2',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-set next_billing_date when service is created
        static::creating(function ($service) {
            if ($service->billing_cycle_id && !$service->next_billing_date) {
                $service->next_billing_date = now()->startOfMonth();
            }
        });

        // Update next_billing_date when billing_cycle_id changes
        static::updating(function ($service) {
            $original = $service->getOriginal();

            // If billing_cycle_id changed and next_billing_date is null, set it
            if ($service->billing_cycle_id != $original['billing_cycle_id'] && !$service->next_billing_date) {
                $service->next_billing_date = now()->startOfMonth();
            }
        });
    }

    // Helper methods for network type
    public function isOdpNetwork(): bool
    {
        return $this->network_type === 'odp';
    }

    public function isWirelessNetwork(): bool
    {
        return $this->network_type === 'wireless';
    }

    public function isHtbNetwork(): bool
    {
        return $this->network_type === 'htb';
    }

    public function getNetworkTypeLabel(): string
    {
        return match ($this->network_type) {
            'odp' => 'Fiber Optik (ODP)',
            'wireless' => 'Wireless/Radio',
            'htb' => 'Hotspot (HTB)',
            default => 'Unknown'
        };
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function odp()
    {
        return $this->belongsTo(Odp::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function billingCycle()
    {
        return $this->belongsTo(BillingCycle::class);
    }

    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'inactive' => 'danger',
            'suspended' => 'warning',
            default => 'secondary',
        };
    }
}
