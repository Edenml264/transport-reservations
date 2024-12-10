<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['route', 'vehicle', 'driver'])
            ->upcoming()
            ->latest('departure_time')
            ->paginate(15);

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $routes = Route::active()->get();
        $vehicles = Vehicle::active()->get();
        $drivers = Driver::active()->validLicense()->get();

        return view('admin.schedules.create', compact('routes', 'vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'departure_time' => 'required|date|after:now',
            'arrival_time' => 'required|date|after:departure_time',
            'current_price' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled'
        ]);

        // Obtener el vehículo para establecer asientos disponibles
        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $validated['available_seats'] = $vehicle->capacity;

        Schedule::create($validated);

        return redirect()->route('schedules.index')
            ->with('success', 'Horario creado correctamente.');
    }

    public function show(Schedule $schedule)
    {
        $schedule->load(['route', 'vehicle', 'driver', 'reservations.user']);
        return view('admin.schedules.show', compact('schedule'));
    }

    public function edit(Schedule $schedule)
    {
        $routes = Route::active()->get();
        $vehicles = Vehicle::active()->get();
        $drivers = Driver::active()->validLicense()->get();

        return view('admin.schedules.edit', 
            compact('schedule', 'routes', 'vehicles', 'drivers'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'current_price' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled'
        ]);

        // Si se cambia el vehículo, actualizar asientos disponibles
        if ($schedule->vehicle_id != $request->vehicle_id) {
            $vehicle = Vehicle::findOrFail($request->vehicle_id);
            $validated['available_seats'] = $vehicle->capacity - 
                $schedule->reservations()->count();
        }

        $schedule->update($validated);

        return redirect()->route('schedules.index')
            ->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy(Schedule $schedule)
    {
        // Verificar si tiene reservaciones
        if ($schedule->reservations()->exists()) {
            return back()->with('error', 
                'No se puede eliminar un horario con reservaciones.');
        }

        $schedule->delete();

        return redirect()->route('schedules.index')
            ->with('success', 'Horario eliminado correctamente.');
    }

    public function search(Request $request)
    {
        $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $schedules = Schedule::whereHas('route', function($query) use ($request) {
            $query->where('origin', 'like', '%' . $request->origin . '%')
                  ->where('destination', 'like', '%' . $request->destination . '%');
        })
        ->whereDate('departure_time', $request->date)
        ->available()
        ->with(['route', 'vehicle'])
        ->get();

        return view('schedules.search', compact('schedules'));
    }
}
