<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_number',
        'name',
        'email',
        'phone',
        'address',
        'province',
        'regency',
        'district',
        'village',
        'hamlet',
        'rt',
        'rw',
        'postal_code',
        'address_notes',
        'identity_number',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];



    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'inactive' => 'danger',
            'suspended' => 'warning',
            default => 'secondary',
        };
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function serviceApplications()
    {
        return $this->hasMany(ServiceApplication::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Service::class);
    }

    /**
     * Generate customer number automatically
     */
    public static function generateCustomerNumber(): string
    {
        $prefix = 'RANET';
        $yearMonth = now()->format('Ym'); // YYYYMM format

        // Get the last customer number for this month
        $lastCustomer = static::where('customer_number', 'like', "{$prefix}-{$yearMonth}-%")
            ->orderBy('customer_number', 'desc')
            ->first();

        if ($lastCustomer) {
            // Extract the sequential number and increment
            $lastNumber = (int) substr($lastCustomer->customer_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            // First customer of the month
            $nextNumber = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $yearMonth, $nextNumber);
    }

    /**
     * Get full formatted address
     */
    public function getFullAddressAttribute(): string
    {
        $addressParts = array_filter([
            $this->address,
            $this->hamlet ? "Dusun {$this->hamlet}" : null,
            $this->rt && $this->rw ? "RT {$this->rt}/RW {$this->rw}" : null,
            $this->village ? "Desa/Kel. {$this->village}" : null,
            $this->district ? "Kec. {$this->district}" : null,
            $this->regency,
            $this->province,
            $this->postal_code,
        ]);

        return implode(', ', $addressParts);
    }

    /**
     * Get administrative area (Kecamatan - Desa - Dusun)
     */
    public function getAdministrativeAreaAttribute(): string
    {
        $areaParts = array_filter([
            $this->district ? "Kec. {$this->district}" : null,
            $this->village ? "Desa {$this->village}" : null,
            $this->hamlet ? "Dusun {$this->hamlet}" : null,
        ]);

        return implode(' - ', $areaParts);
    }

    /**
     * Get coverage area for technical management
     */
    public function getCoverageAreaAttribute(): string
    {
        return $this->district . ($this->village ? " - {$this->village}" : '');
    }

    /**
     * Scope: Filter by district (Kecamatan)
     */
    public function scopeByDistrict($query, $district)
    {
        return $query->where('district', $district);
    }

    /**
     * Scope: Filter by village (Desa/Kelurahan)
     */
    public function scopeByVillage($query, $village)
    {
        return $query->where('village', $village);
    }

    /**
     * Scope: Filter by hamlet (Dusun)
     */
    public function scopeByHamlet($query, $hamlet)
    {
        return $query->where('hamlet', $hamlet);
    }

    /**
     * Scope: Filter by coverage area (District + Village)
     */
    public function scopeByCoverageArea($query, $district, $village = null)
    {
        $query->where('district', $district);

        if ($village) {
            $query->where('village', $village);
        }

        return $query;
    }

    /**
     * Boot method to auto-generate customer number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_number)) {
                $customer->customer_number = static::generateCustomerNumber();
            }
        });
    }
}
