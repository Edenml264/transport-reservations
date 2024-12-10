<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Route;
use App\Models\Schedule;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $routes = Route::active()->get();
        $featuredSchedules = Schedule::with(['route', 'vehicle'])
            ->upcoming()
            ->available()
            ->take(5)
            ->get();

        return view('home', compact('routes', 'featuredSchedules'));
    }

    public function profile()
    {
        $user = auth()->user();
        $reservations = $user->reservations()
            ->with(['schedule.route', 'payment'])
            ->latest()
            ->paginate(10);

        return view('profile', compact('user', 'reservations'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $user = auth()->user();
        $user->update($request->only(['name', 'phone']));

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
