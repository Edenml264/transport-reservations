<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Estadísticas generales
        $stats = [
            'total_reservations' => Reservation::count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
            'total_income' => Payment::where('status', 'completed')->sum('amount'),
            'active_vehicles' => Vehicle::where('status', 'active')->count(),
            'active_drivers' => Driver::where('status', 'active')->count(),
        ];

        // Próximos viajes
        $upcomingSchedules = Schedule::with(['route', 'vehicle', 'driver'])
            ->upcoming()
            ->take(5)
            ->get();

        // Últimas reservaciones
        $latestReservations = Reservation::with(['user', 'schedule.route'])
            ->latest()
            ->take(10)
            ->get();

        // Ingresos por mes (últimos 6 meses)
        $monthlyIncome = Payment::where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'upcomingSchedules', 
            'latestReservations', 
            'monthlyIncome'
        ));
    }

    public function reservationsReport(Request $request)
    {
        $query = Reservation::with(['user', 'schedule.route', 'payment']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $reservations = $query->latest()->paginate(20);

        // Totales
        $totals = [
            'count' => $reservations->total(),
            'amount' => $query->sum('total_price'),
            'paid' => Payment::whereIn('reservation_id', $query->pluck('id'))
                           ->where('status', 'completed')
                           ->sum('amount')
        ];

        return view('admin.reports.reservations', compact('reservations', 'totals'));
    }

    public function paymentsReport(Request $request)
    {
        $query = Payment::with(['reservation.user', 'reservation.schedule.route']);

        // Filtros
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(20);

        // Totales por método de pago
        $totalsByMethod = Payment::where('status', 'completed')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        return view('admin.reports.payments', compact('payments', 'totalsByMethod'));
    }
}
