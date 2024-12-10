<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::withCount(['schedules' => function($query) {
            $query->upcoming();
        }])->latest()->paginate(10);
        
        return view('admin.routes.index', compact('routes'));
    }

    public function create()
    {
        return view('admin.routes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'distance' => 'required|numeric|min:0',
            'estimated_duration' => 'required|integer|min:1',
            'base_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string'
        ]);

        Route::create($validated);

        return redirect()->route('routes.index')
            ->with('success', 'Ruta creada correctamente.');
    }

    public function show(Route $route)
    {
        $route->load(['schedules' => function($query) {
            $query->upcoming()->with(['vehicle', 'driver']);
        }]);
        
        return view('admin.routes.show', compact('route'));
    }

    public function edit(Route $route)
    {
        return view('admin.routes.edit', compact('route'));
    }

    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'distance' => 'required|numeric|min:0',
            'estimated_duration' => 'required|integer|min:1',
            'base_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string'
        ]);

        $route->update($validated);

        return redirect()->route('routes.index')
            ->with('success', 'Ruta actualizada correctamente.');
    }

    public function destroy(Route $route)
    {
        // Verificar si tiene viajes programados
        if ($route->schedules()->upcoming()->exists()) {
            return back()->with('error', 'No se puede eliminar una ruta con viajes programados.');
        }

        $route->delete();

        return redirect()->route('routes.index')
            ->with('success', 'Ruta eliminada correctamente.');
    }
}