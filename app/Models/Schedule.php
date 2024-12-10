<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'route_id',
        'vehicle_id',
        'driver_id',
        'departure_time',
        'arrival_time',
        'status',
        'available_seats',
        'current_price'
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'current_price' => 'decimal:2'
    ];

    // Relaciones
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('departure_time', '>', now());
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('available_seats', '>', 0);
    }
}
