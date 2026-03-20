<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title'); // "Boda - Juan Pérez"
            $table->enum('status', ['confirmed', 'tentative', 'in_progress', 'completed', 'cancelled'])->default('confirmed');
            $table->string('color', 7)->default('#f59e0b'); // Hex for calendar display

            // Event info
            $table->string('event_type_label')->nullable();
            $table->date('event_date');
            $table->string('time_start')->nullable();
            $table->string('time_end')->nullable();
            $table->string('venue')->nullable();
            $table->string('city')->nullable();
            $table->boolean('is_outdoor')->default(false);
            $table->integer('guest_count')->nullable();
            $table->integer('hours_contracted')->default(5);

            // Client info (denormalized for quick access)
            $table->string('client_name');
            $table->string('client_phone')->nullable();

            // Notes
            $table->text('notes')->nullable();
            $table->text('setup_notes')->nullable(); // technical / setup instructions

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
