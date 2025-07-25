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
        Schema::create('ambientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->integer('aulas')->nullable();
            $table->integer('laboratorios')->nullable();
            $table->integer('bibliotecas')->nullable();
            $table->integer('computacion')->nullable();
            $table->integer('canchas')->nullable();
            $table->integer('gimnasios')->nullable();

            $table->integer('coliseos')->nullable();
            $table->integer('piscinas')->nullable();
            $table->integer('secretaria')->nullable();
            $table->integer('reuniones')->nullable();
            $table->integer('talleres')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ambientes');
    }
};
