<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'speed',
        'price',
        'description',
        'technology_type',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'technology_type' => 'string',
    ];

    public function requiresOdp(): bool
    {
        return in_array($this->technology_type, ['fiber', 'hybrid']);
    }

    public function isWireless(): bool
    {
        return in_array($this->technology_type, ['wireless', 'hybrid']);
    }

    /**
     * Get customers through services
     */
    public function customers()
    {
        return $this->hasManyThrough(Customer::class, Service::class);
    }

    /**
     * Get services using this package
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
