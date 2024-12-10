<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'license_number',
        'phone',
        'status',
        'documents',
        'license_expiration',
        'notes'
    ];

    protected $casts = [
        'documents' => 'array',
        'license_expiration' => 'date'
    ];

    // Relaciones
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeValidLicense($query)
    {
        return $query->where('license_expiration', '>', now());
    }
}
