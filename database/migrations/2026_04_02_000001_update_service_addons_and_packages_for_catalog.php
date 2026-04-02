<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_addons', function (Blueprint $table) {
            $table->string('subcategory')->nullable()->after('category');
            $table->decimal('supplier_price', 10, 2)->nullable()->after('price');
            $table->string('duration')->nullable()->after('unit');
            $table->decimal('price', 10, 2)->nullable()->change();
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->change();
            $table->integer('duration_hours')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('service_addons', function (Blueprint $table) {
            $table->dropColumn(['subcategory', 'supplier_price', 'duration']);
            $table->decimal('price', 10, 2)->nullable(false)->change();
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable(false)->change();
            $table->integer('duration_hours')->default(5)->nullable(false)->change();
        });
    }
};
