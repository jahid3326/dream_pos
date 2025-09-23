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
        Schema::table('sale_payments', function (Blueprint $table) {

            $table->unsignedBigInteger('quote_id')->nullable()->after('sale_id');

            // 2. Add the foreign key constraint
            $table->foreign('quote_id')
                ->references('id')
                ->on('quotes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->dropForeign(['quote_id']);
            $table->dropColumn('quote_id');
        });
    }
};
