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
        Schema::create('product_without_barcodes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del producto
            $table->string('category')->nullable(); // Categoría del producto
            $table->string('barcode')->nullable()->unique(); // Código de barras generado (puede ser nulo inicialmente)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_without_barcodes');
    }
};
