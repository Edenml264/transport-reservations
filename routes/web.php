<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;

// Rutas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [ScheduleController::class, 'search'])->name('schedule.search');

// Rutas de autenticación
Auth::routes();

// Rutas para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    // Reservaciones
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create/{schedule}', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

    // Pagos
    Route::get('/payments/{reservation}', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

    // Perfil de usuario
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::put('/profile', [HomeController::class, 'updateProfile'])->name('profile.update');
});

// Rutas para administradores
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Gestión de vehículos
    Route::resource('vehicles', VehicleController::class);
    
    // Gestión de conductores
    Route::resource('drivers', DriverController::class);
    
    // Gestión de rutas
    Route::resource('routes', RouteController::class);
    
    // Gestión de horarios
    Route::resource('schedules', ScheduleController::class);
    
    // Reportes
    Route::get('/reports/reservations', [AdminController::class, 'reservationsReport'])->name('admin.reports.reservations');
    Route::get('/reports/payments', [AdminController::class, 'paymentsReport'])->name('admin.reports.payments');
});

// Rutas para PayPal
Route::post('/paypal/webhook', [PaymentController::class, 'handleWebhook'])->name('paypal.webhook');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
