@extends('layouts.app')

@section('title', 'Detalles de Reservación')

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('reservations.index') }}" class="text-decoration-none">
            <i class="bi bi-arrow-left"></i> Volver a mis reservaciones
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Reservación #{{ $reservation->id }}</h1>
                <span class="badge bg-{{ $reservation->status_color }} fs-6">
                    {{ $reservation->status_text }}
                </span>
            </div>
        </div>

        <div class="card-body">
            <!-- Detalles del Viaje -->
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">Detalles del Viaje</h4>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1 text-muted">Origen</p>
                                    <h5>{{ $reservation->schedule->route->origin }}</h5>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 text-muted">Destino</p>
                                    <h5>{{ $reservation->schedule->route->destination }}</h5>
                                </div>
                            </div>
                            <hr>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <i class="bi bi-calendar-event text-primary"></i>
                                    <strong>Fecha de Salida:</strong>
                                    {{ $reservation->schedule->departure_time->format('d/m/Y') }}
                                </li>
                                <li class="mb-3">
                                    <i class="bi bi-clock text-primary"></i>
                                    <strong>Hora de Salida:</strong>
                                    {{ $reservation->schedule->departure_time->format('H:i') }}
                                </li>
                                <li class="mb-3">
                                    <i class="bi bi-stopwatch text-primary"></i>
                                    <strong>Duración Estimada:</strong>
                                    {{ $reservation->schedule->route->formatted_duration }}
                                </li>
                                <li>
                                    <i class="bi bi-person text-primary"></i>
                                    <strong>Asiento(s):</strong>
                                    {{ $reservation->seats->pluck('number')->join(', ') }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Información del Vehículo -->
                    <h4 class="card-title">Vehículo</h4>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1 text-muted">Modelo</p>
                                    <h5>{{ $reservation->schedule->vehicle->model }}</h5>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 text-muted">Placa</p>
                                    <h5>{{ $reservation->schedule->vehicle->plate_number }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Conductor -->
                    <h4 class="card-title">Conductor</h4>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5>{{ $reservation->schedule->driver->name }}</h5>
                            <p class="text-muted mb-0">Licencia: {{ $reservation->schedule->driver->license_number }}</p>
                        </div>
                    </div>
                </div>

                <!-- Resumen de Pago -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h4 class="card-title mb-0">Resumen de Pago</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="d-flex justify-content-between mb-3">
                                    <span>Precio por asiento:</span>
                                    <strong>${{ number_format($reservation->price_per_seat, 2) }}</strong>
                                </li>
                                <li class="d-flex justify-content-between mb-3">
                                    <span>Número de asientos:</span>
                                    <strong>{{ $reservation->seats->count() }}</strong>
                                </li>
                                @if($reservation->discount > 0)
                                <li class="d-flex justify-content-between mb-3">
                                    <span>Descuento:</span>
                                    <strong class="text-success">-${{ number_format($reservation->discount, 2) }}</strong>
                                </li>
                                @endif
                                <li class="d-flex justify-content-between border-top pt-3">
                                    <span class="h5">Total:</span>
                                    <strong class="h5">${{ number_format($reservation->total_price, 2) }}</strong>
                                </li>
                            </ul>

                            @if($reservation->payment)
                                <div class="alert alert-{{ $reservation->payment->status === 'completed' ? 'success' : 'warning' }} mb-0">
                                    <i class="bi bi-info-circle"></i>
                                    Estado del pago: {{ $reservation->payment->status_text }}
                                    @if($reservation->payment->transaction_id)
                                        <br>
                                        <small>ID de Transacción: {{ $reservation->payment->transaction_id }}</small>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning mb-3">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Pago pendiente
                                </div>
                                <div class="d-grid">
                                    <a href="{{ route('payments.create', $reservation) }}" 
                                       class="btn btn-primary">
                                        Realizar Pago
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($reservation->canBeCancelled())
                        <form action="{{ route('reservations.cancel', $reservation) }}" 
                              method="POST" 
                              class="mt-3"
                              onsubmit="return confirm('¿Estás seguro de cancelar esta reservación?')">
                            @csrf
                            @method('PATCH')
                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-danger">
                                    Cancelar Reservación
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bi {
        margin-right: 8px;
    }
</style>
@endpush