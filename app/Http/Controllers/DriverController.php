<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::latest()->paginate(10);
        return view('admin.drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('admin.drivers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|unique:drivers',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
            'license_expiration' => 'required|date|after:today',
            'notes' => 'nullable|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        // Procesar documentos si se subieron
        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('driver-documents', 'public');
                $documents[] = $path;
            }
        }

        $validated['documents'] = $documents;
        Driver::create($validated);

        return redirect()->route('drivers.index')
            ->with('success', 'Conductor registrado correctamente.');
    }

    public function show(Driver $driver)
    {
        $driver->load(['schedules' => function($query) {
            $query->upcoming();
        }]);
        
        return view('admin.drivers.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        return view('admin.drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|unique:drivers,license_number,' . $driver->id,
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
            'license_expiration' => 'required|date|after:today',
            'notes' => 'nullable|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        // Procesar nuevos documentos si se subieron
        if ($request->hasFile('documents')) {
            $documents = $driver->documents ?? [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('driver-documents', 'public');
                $documents[] = $path;
            }
            $validated['documents'] = $documents;
        }

        $driver->update($validated);

        return redirect()->route('drivers.index')
            ->with('success', 'Conductor actualizado correctamente.');
    }

    public function destroy(Driver $driver)
    {
        // Verificar si tiene viajes programados
        if ($driver->schedules()->upcoming()->exists()) {
            return back()->with('error', 'No se puede eliminar un conductor con viajes programados.');
        }

        $driver->delete();

        return redirect()->route('drivers.index')
            ->with('success', 'Conductor eliminado correctamente.');
    }
}
