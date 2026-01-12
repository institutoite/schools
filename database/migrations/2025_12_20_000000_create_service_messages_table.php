<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('service_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message', 255);
            $table->string('service', 25);
            $table->string('whatsapp', 500)->nullable();
            $table->string('boton', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_messages');
    }
};
