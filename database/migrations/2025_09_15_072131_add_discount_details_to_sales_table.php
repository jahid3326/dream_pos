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
        Schema::table('sales', function (Blueprint $table) {
            // This will store 'fixed' or 'percentage'
            $table->string('discount_type')->nullable()->after('discount');

            // This will store the percentage rate if applicable (e.g., 10 for 10%)
            $table->decimal('discount_rate', 5, 2)->nullable()->after('discount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_rate']);
        });
    }
};
