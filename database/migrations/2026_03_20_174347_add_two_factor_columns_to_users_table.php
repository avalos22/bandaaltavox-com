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
        Schema::table('users', function (Blueprint $table) {
            $table->string('two_factor_type')->nullable()->after('is_active'); // totp, email, or null
            $table->text('two_factor_secret')->nullable()->after('two_factor_type');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_secret');
            $table->string('two_factor_email_code', 6)->nullable()->after('two_factor_confirmed_at');
            $table->timestamp('two_factor_email_expires_at')->nullable()->after('two_factor_email_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_type',
                'two_factor_secret',
                'two_factor_confirmed_at',
                'two_factor_email_code',
                'two_factor_email_expires_at',
            ]);
        });
    }
};
