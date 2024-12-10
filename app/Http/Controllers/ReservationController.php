<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\Seat;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = auth()->user()->reservations()
            ->with(['schedule.route', 'seat', 'payment'])
            ->latest()
            ->paginate(10);

        return view('reservations.index', compact('reservations'));
    }

    public function create(Schedule $schedule)
    {
        $availableSeats = Seat::where('vehicle_id', $schedule->vehicle_id)
            ->available()
            ->get();

        return view('reservations.create', compact('schedule', 'availableSeats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'seat_id' => 'required|exists:seats,id',
            'special_requests' => 'nullable|string|max:500',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);
        
        // Verificar disponibilidad
        if ($schedule->available_seats <= 0) {
            return back()->with('error', 'Lo sentimos, no hay asientos disponibles.');
        }

        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'schedule_id' => $request->schedule_id,
            'seat_id' => $request->seat_id,
            'status' => 'pending',
            'total_price' => $schedule->current_price,
            'reservation_date' => now(),
            'special_requests' => $request->special_requests,
        ]);

        // Actualizar asiento y cantidad disponible
        $seat = Seat::findOrFail($request->seat_id);
        $seat->update(['status' => 'occupied']);
        $schedule->decrement('available_seats');

        return redirect()->route('payments.create', $reservation)
            ->with('success', 'Reservación creada. Por favor, proceda con el pago.');
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        
        $reservation->load(['schedule.route', 'seat', 'payment']);
        
        return view('reservations.show', compact('reservation'));
    }

    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        if ($reservation->status === 'confirmed') {
            return back()->with('error', 'No se puede cancelar una reservación confirmada.');
        }

        $reservation->seat->update(['status' => 'available']);
        $reservation->schedule->increment('available_seats');
        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservación cancelada correctamente.');
    }
}
