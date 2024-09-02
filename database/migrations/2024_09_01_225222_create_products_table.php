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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // numeración
            $table->string('order')->unique();
            $table->string('barcode')->unique();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('category');
            $table->enum('unit_type', ['unit', 'package']); // El campo que indica si viene en paquete o por unidad
            $table->decimal('purchase_price', 8, 2);
            $table->decimal('profit_margin', 5, 2); // porcentaje de ganancia
            $table->decimal('sale_price', 8, 2);
            $table->integer('stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
