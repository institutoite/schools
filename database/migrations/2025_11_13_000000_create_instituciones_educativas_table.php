<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instituciones_educativas', function (Blueprint $table) {
            $table->id();
            $table->string('departamento', 50);
            $table->string('distrito', 100)->nullable();
            $table->string('codigo', 25)->unique();
            $table->string('nombre', 150);
            $table->string('educacion', 100)->nullable(); // Nivel / modalidad
            $table->enum('dependencia', ['FISCAL','PRIVADO','CONVENIO'])->nullable();
            $table->string('resolucion', 100)->nullable();
            $table->date('fecha_resolucion')->nullable();
            $table->string('zona', 120)->nullable();
            $table->string('direccion', 180)->nullable();
            $table->enum('estado', ['ACTIVO','INACTIVO','CERRADO'])->default('ACTIVO');
            $table->timestamps();

            $table->index(['departamento', 'distrito']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instituciones_educativas');
    }
};