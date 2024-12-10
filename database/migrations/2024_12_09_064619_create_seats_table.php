<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->string('seat_number');
            $table->enum('type', ['regular', 'premium', 'vip'])->default('regular');
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->timestamps();
            
            // Aseguramos que no haya números de asiento duplicados en el mismo vehículo
            $table->unique(['vehicle_id', 'seat_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
