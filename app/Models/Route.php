<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Route extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'origin',
        'destination',
        'distance',
        'estimated_duration',
        'base_price',
        'status',
        'description'
    ];

    protected $casts = [
        'distance' => 'decimal:2',
        'base_price' => 'decimal:2',
        'estimated_duration' => 'integer'
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

    public function getFormattedDurationAttribute()
    {
        return floor($this->estimated_duration / 60) . 'h ' . ($this->estimated_duration % 60) . 'm';
    }
}
