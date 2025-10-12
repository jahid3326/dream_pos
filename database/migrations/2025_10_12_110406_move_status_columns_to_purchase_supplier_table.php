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
        Schema::table('purchase_supplier', function (Blueprint $table) {
            $table->string('status_review')->default('pending')->after('supplier_id');
            $table->string('status_production')->default('waiting')->after('status_review');
        });

        // 2. Drop the old columns from the main purchases table
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['status_review', 'status_production']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the process on rollback
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('status_review')->default('pending');
            $table->string('status_production')->default('waiting');
        });

        Schema::table('purchase_supplier', function (Blueprint $table) {
            $table->dropColumn(['status_review', 'status_production']);
        });
    }
};
