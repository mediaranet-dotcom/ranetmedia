<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Odp extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'address',
        'latitude',
        'longitude',
        'area',
        'district',
        'total_ports',
        'used_ports',
        'available_ports',
        'odp_type',
        'manufacturer',
        'model',
        'feeder_cable',
        'fiber_count',
        'splitter_ratio',
        'status',
        'condition',
        'installation_date',
        'last_maintenance',
        'notes',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'total_ports' => 'integer',
        'used_ports' => 'integer',
        'available_ports' => 'integer',
        'fiber_count' => 'integer',
        'installation_date' => 'date',
        'last_maintenance' => 'date',
    ];

    // Relationships
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // Helper Methods
    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'maintenance' => 'warning',
            'damaged' => 'danger',
            default => 'secondary',
        };
    }

    public function getConditionBadgeColor(): string
    {
        return match ($this->condition) {
            'excellent' => 'success',
            'good' => 'info',
            'fair' => 'warning',
            'poor' => 'danger',
            'damaged' => 'danger',
            default => 'secondary',
        };
    }

    public function getUtilizationPercentage(): float
    {
        if ($this->total_ports == 0) return 0;
        return round(($this->used_ports / $this->total_ports) * 100, 2);
    }

    public function isNearCapacity(): bool
    {
        return $this->getUtilizationPercentage() >= 80;
    }

    public function updatePortUsage(): void
    {
        $this->used_ports = $this->services()->count();
        $this->available_ports = $this->total_ports - $this->used_ports;
        $this->save();
    }
}
