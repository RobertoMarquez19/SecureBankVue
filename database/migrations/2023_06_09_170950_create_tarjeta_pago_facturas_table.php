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
        Schema::create('tarjeta_pago_facturas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('from_tarjeta_id')->nullable(false);
            $table->unsignedBigInteger('to_factura_id')->nullable(false);
            $table->double('from_tarjeta_monto_antes')->nullable(false);
            $table->double('from_tarjeta_monto_despues')->nullable(false);
            $table->unique(['from_tarjeta_id','to_factura_id']);
            $table->foreign('from_tarjeta_id')->references('id')->on('tarjeta_creditos');
            $table->foreign('to_factura_id')->references('id')->on('facturas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjeta_pago_facturas');
    }
};
