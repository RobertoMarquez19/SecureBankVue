<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cuenta_bancarias', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('numero_cuenta')->unique()->nullable(false);
            $table->double('monto_cuenta')->nullable(false);
            $table->dateTime('fecha_apertura')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('estado_cuenta',['activa','cerrada','bloqueada','inactiva']);
            $table->unsignedBigInteger('id_producto')->nullable(false);
            $table->foreign('id_producto')->references('id')->on('productos');
            $table->unsignedBigInteger('id_cliente')->nullable(false);
            $table->foreign('id_cliente')->references('id')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuenta_bancarias');
    }
};
