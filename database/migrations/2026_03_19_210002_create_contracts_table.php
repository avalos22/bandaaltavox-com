<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique(); // CONT-2026-0001
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');

            // Contract date
            $table->date('contract_date');

            // Client info (snapshot from quotation)
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('client_address')->nullable();

            // Event info (snapshot)
            $table->string('event_type_label')->nullable();
            $table->date('event_date')->nullable();
            $table->string('event_time_start')->nullable();
            $table->string('event_time_end')->nullable();
            $table->string('event_venue')->nullable();
            $table->integer('hours_contracted')->default(5);
            $table->boolean('event_is_outdoor')->default(false);

            // Financial
            $table->decimal('total_amount', 10, 2);
            $table->decimal('first_payment_amount', 10, 2)->default(0);
            $table->date('first_payment_date')->nullable();

            // Contract content
            $table->text('observations')->nullable();
            $table->json('clauses')->nullable(); // Array of clause strings
            $table->string('representative_name')->default('Cristian García G.');

            // Signatures
            $table->timestamp('signed_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
