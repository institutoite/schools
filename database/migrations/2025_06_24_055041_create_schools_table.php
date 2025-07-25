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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_rue',20)->unique();
            $table->string('nombre',100);
            $table->string('director',100)->nullable();
            $table->string('direccion',150)->nullable();
            $table->string('telefonos',35)->nullable();
            $table->enum('dependencia', ['FISCAL', 'PRIVADO', 'CONVENIO'])->default('FISCAL');
            $table->string('niveles',70)->nullable(); // Inicial/Primaria/Secundaria
            $table->string('turnos',50)->nullable(); // MAÑANA/TARDE/NOCHE
            $table->string('url_ficha',100)->nullable();
            $table->string('humanistico',10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
