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
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('discount_percent', 5, 2)->default(0); // Descuento en porcentaje
            $table->decimal('surcharge_percent', 5, 2)->default(0); // Recargo en porcentaje
            $table->decimal('total_before_adjustments', 10, 2); // Total original antes de descuentos y recargos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['discount_percent', 'surcharge_percent', 'total_before_adjustments']); // Eliminar todas las columnas
        });
    }
};
