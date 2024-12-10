@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row align-items-center py-5">
        <div class="col-md-6">
            <h1 class="display-4 fw-bold">Viaja con comodidad y seguridad</h1>
            <p class="lead">Reserva tu viaje de manera fácil y rápida. Encuentra los mejores horarios y precios.</p>
            <div class="mt-4">
                <a href="{{ route('schedule.search') }}" class="btn btn-primary btn-lg">Buscar Viajes</a>
            </div>
        </div>
        <div class="col-md-6">
            <img src="{{ asset('images/bus-hero.jpg') }}" alt="Transporte" class="img-fluid rounded shadow">
        </div>
    </div>

    <!-- Search Form -->
    <div class="card mt-5 shadow">
        <div class="card-body">
            <h3 class="card-title text-center mb-4">Encuentra tu próximo viaje</h3>
            <form action="{{ route('schedule.search') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="origin" class="form-label">Origen</label>
                        <input type="text" class="form-control" id="origin" name="origin" required>
                    </div>
                    <div class="col-md-4">
                        <label for="destination" class="form-label">Destino</label>
                        <input type="text" class="form-control" id="destination" name="destination" required>
                    </div>
                    <div class="col-md-4">
                        <label for="date" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="date" name="date" 
                               min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary px-5">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Featured Routes -->
    @if($featuredSchedules->count() > 0)
    <div class="mt-5">
        <h2 class="text-center mb-4">Próximos Viajes Disponibles</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach($featuredSchedules as $schedule)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $schedule->route->origin }} - {{ $schedule->route->destination }}</h5>
                        <p class="card-text">
                            <i class="bi bi-calendar"></i> {{ $schedule->departure_time->format('d/m/Y H:i') }}<br>
                            <i class="bi bi-clock"></i> Duración: {{ $schedule->route->formatted_duration }}<br>
                            <i class="bi bi-cash"></i> Precio: ${{ number_format($schedule->current_price, 2) }}
                        </p>
                        <div class="d-grid">
                            <a href="{{ route('reservations.create', $schedule) }}" 
                               class="btn btn-outline-primary">Reservar</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Features Section -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mt-5">
        <div class="col">
            <div class="card h-100 border-0 text-center">
                <div class="card-body">
                    <i class="bi bi-shield-check display-4 text-primary"></i>
                    <h4 class="card-title mt-3">Viajes Seguros</h4>
                    <p class="card-text">Conductores profesionales y vehículos en excelente estado.</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 text-center">
                <div class="card-body">
                    <i class="bi bi-credit-card display-4 text-primary"></i>
                    <h4 class="card-title mt-3">Pago Seguro</h4>
                    <p class="card-text">Realiza tus pagos de forma segura a través de PayPal.</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 text-center">
                <div class="card-body">
                    <i class="bi bi-headset display-4 text-primary"></i>
                    <h4 class="card-title mt-3">Soporte 24/7</h4>
                    <p class="card-text">Atención al cliente disponible en todo momento.</p>
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
