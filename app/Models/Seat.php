<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Vehicle;
use App\Models\Reservation;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'seat_number',
        'type',
        'status'
    ];

    // Relaciones
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
