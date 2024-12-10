@extends('layouts.app')

@section('title', 'Mis Reservaciones')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2">Mis Reservaciones</h1>
        </div>
        <div class="col text-end">
            <a href="{{ route('schedule.search') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nueva Reservación
            </a>
        </div>
    </div>

    @if($reservations->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x display-1 text-muted"></i>
                <h3 class="mt-3">No tienes reservaciones</h3>
                <p class="text-muted">Comienza reservando tu primer viaje</p>
                <a href="{{ route('schedule.search') }}" class="btn btn-primary">
                    Buscar Viajes
                </a>
            </div>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 g-4">
            @foreach($reservations as $reservation)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                {{ $reservation->schedule->route->origin }} - 
                                {{ $reservation->schedule->route->destination }}
                            </h5>
                            <span class="badge bg-{{ $reservation->status_color }}">
                                {{ $reservation->status_text }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-calendar"></i>
                                {{ $reservation->schedule->departure_time->format('d/m/Y H:i') }}
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-person"></i>
                                {{ $reservation->passenger_count }} pasajero(s)
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-cash"></i>
                                ${{ number_format($reservation->total_price, 2) }}
                            </li>
                            @if($reservation->payment)
                            <li>
                                <i class="bi bi-credit-card"></i>
                                Pago: {{ $reservation->payment->status_text }}
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-grid gap-2">
                            <a href="{{ route('reservations.show', $reservation) }}" 
                               class="btn btn-outline-primary">
                                Ver Detalles
                            </a>
                            @if($reservation->canBeCancelled())
                            <form action="{{ route('reservations.cancel', $reservation) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('¿Estás seguro de cancelar esta reservación?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    Cancelar Reservación
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $reservations->links() }}
        </div>
    @endif
</div>
@endsection