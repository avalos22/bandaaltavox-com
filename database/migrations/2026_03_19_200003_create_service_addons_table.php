<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // "Hora extra Norteño", "Pantalla LED", "Máquina de CO2"
            $table->string('category');                // extra_hours, equipment, effects, other
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);           // 5500.00
            $table->string('unit')->default('por evento'); // por hora, por evento, por pieza
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_addons');
    }
};
