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
        // 1. Add the new column to the pivot table
        Schema::table('purchase_supplier', function (Blueprint $table) {
            $table->date('ready_date')->nullable()->after('status_production');
        });

        // 2. Drop the old column from the main purchases table
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('ready_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the process on rollback
        Schema::table('purchases', function (Blueprint $table) {
            $table->date('ready_date')->nullable();
        });

        Schema::table('purchase_supplier', function (Blueprint $table) {
            $table->dropColumn('ready_date');
        });
    }
};
