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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('dui')->nullable(false);
            $table->string('dui_hash')->unique()->nullable(false);
            $table->string('nit')->nullable(false);
            $table->string('nit_hash')->unique()->nullable(false);
            $table->string('nombres')->nullable(false);
            $table->string('apellidos')->nullable(false);
            $table->date('fecha_nacimiento')->nullable(false);
            $table->string('email')->nullable(false);
            $table->string('email_hash')->unique()->nullable(false);
            $table->string('telefono')->nullable(false);
            $table->string('telefono_hash')->unique()->nullable(false);
            $table->string('telefono_trabajo')->nullable(true);
            $table->string('direccion')->nullable(false);
            $table->enum('genero',['M','F'])->nullable(false);
            $table->enum('estado_civil',['Soltero','Casado','Divorciado','Viudo'])->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
