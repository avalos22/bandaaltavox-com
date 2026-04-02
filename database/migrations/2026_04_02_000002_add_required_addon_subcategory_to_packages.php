<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Subcategory slug that must be selected from addons when this package is chosen.
            // e.g. 'Grupo musical' — references ServiceAddon.subcategory values.
            $table->string('required_addon_subcategory')->nullable()->after('duration_hours');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('required_addon_subcategory');
        });
    }
};
