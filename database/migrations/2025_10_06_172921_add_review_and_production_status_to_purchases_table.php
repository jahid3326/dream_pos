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
        Schema::table('purchases', function (Blueprint $table) {
            // Adds the 'status_review' column after the existing 'status' column.
            // Example values: 'pending', 'need review supplier', 'need accepte review customer', 'complet'
            $table->string('status_review')->default('pending')->after('status');

            // Adds the 'status_production' column after the new 'status_review' column.
            // Example values: 'waiting', 'in process', 'complet'
            $table->string('status_production')->default('waiting')->after('status_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Drop the columns in the reverse order they were added.
            $table->dropColumn(['status_production', 'status_review']);
        });
    }
};
