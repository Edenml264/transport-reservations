<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with(['seats'])
            ->latest()
            ->paginate(10);

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number',
            'model' => 'required|string',
            'brand' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'year' => 'required|integer|min:1900',
            'status' => 'required|in:active,maintenance,inactive',
            'description' => 'nullable|string'
        ]);

        $vehicle = Vehicle::create($validated);

        // Crear asientos automáticamente
        for ($i = 1; $i <= $vehicle->capacity; $i++) {
            $vehicle->seats()->create([
                'seat_number' => sprintf('%02d', $i),
                'type' => 'regular',
                'status' => 'available'
            ]);
        }

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo creado correctamente.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['seats', 'schedules']);
        return view('admin.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'model' => 'required|string',
            'brand' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'year' => 'required|integer|min:1900',
            'status' => 'required|in:active,maintenance,inactive',
            'description' => 'nullable|string'
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehicle $vehicle)
    {
        // Verificar si tiene viajes programados
        if ($vehicle->schedules()->upcoming()->exists()) {
            return back()->with('error', 'No se puede eliminar un vehículo con viajes programados.');
        }

        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo eliminado correctamente.');
    }
}
