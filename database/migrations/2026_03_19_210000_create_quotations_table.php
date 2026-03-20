<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique(); // COT-2026-0001
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired', 'converted'])->default('draft');

            // Client info (may not be a registered user yet)
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('client_address')->nullable();

            // Event info
            $table->foreignId('event_type_id')->nullable()->constrained()->nullOnDelete();
            $table->date('event_date')->nullable();
            $table->string('event_time_start')->nullable(); // "21:30"
            $table->string('event_time_end')->nullable();   // "23:30"
            $table->string('event_venue')->nullable();
            $table->string('event_city')->nullable();
            $table->boolean('event_is_outdoor')->default(false);
            $table->integer('guest_count')->nullable();
            $table->integer('hours_contracted')->default(5);

            // Financial
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            // Meta
            $table->text('observations')->nullable();
            $table->integer('validity_days')->default(15);
            $table->date('expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
