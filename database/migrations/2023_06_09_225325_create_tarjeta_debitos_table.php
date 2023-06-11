<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tarjeta_debitos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('numero_tarjeta')->nullable(false);
            $table->string('numero_tarjeta_hash')->unique()->nullable(false);
            $table->dateTime('fecha_emision')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('fecha_vencimiento')->nullable(false)->default(DB::raw("DATEADD(year, 5, GETDATE())"));
            $table->string('cvv')->nullable(false);
            $table->unsignedBigInteger('id_tipo_tarjeta')->nullable(false);
            $table->foreign('id_tipo_tarjeta')->references('id')->on('tarjeta_tipos');
            $table->unsignedBigInteger('id_cuenta')->nullable(false);
            $table->foreign('id_cuenta')->references('id')->on('cuenta_bancarias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjeta_debitos');
    }
};
