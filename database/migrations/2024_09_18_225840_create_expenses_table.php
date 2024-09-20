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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('description'); // Descripción del egreso
            $table->decimal('amount', 10, 2); // Monto del egreso
            $table->enum('type', ['compra de productos', 'insumos', 'impuestos', 'proveedores', 'servicios', 'comida', 'transporte', 'otros']); // Tipo de egreso
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
