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
        Schema::create('transacciones_cuentas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('from_cuenta_id')->nullable(false);
            $table->unsignedBigInteger('to_cuenta_id')->nullable(false);
            $table->double('monto')->nullable(false);
            $table->double('from_cuenta_monto_antes')->nullable(false);
            $table->double('from_cuenta_monto_despues')->nullable(false);
            $table->double('to_cuenta_monto_antes')->nullable(false);
            $table->double('to_cuenta_monto_despues')->nullable(false);
            $table->text('descripcion')->nullable(false);
            $table->foreign('from_cuenta_id')->references('id')->on('cuenta_bancarias');
            $table->foreign('to_cuenta_id')->references('id')->on('cuenta_bancarias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones_cuentas');
    }
};
