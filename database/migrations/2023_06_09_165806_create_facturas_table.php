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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('npe')->unique()->nullable(false);
            $table->double('monto')->nullable(false);
            $table->dateTime('vencimiento')->nullable(false);
            $table->string('colector')->nullable(false);
            $table->enum('estado',['pendiente','vencido','pagado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
