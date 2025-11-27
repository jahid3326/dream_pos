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
        Schema::table('shipments', function (Blueprint $table) {
            $table->date('delivery_estimation_date')->nullable()->after('shipment_date');
            $table->enum('type_shipping1', ['By Sea', 'By Air', 'By Train'])->nullable()->after('delivery_estimation_date');
            $table->enum('type_shipping2', ['DDP', 'DDU', 'CIF'])->nullable()->after('type_shipping1');
            $table->json('container')->nullable()->after('type_shipping2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['delivery_estimation_date', 'type_shipping1', 'type_shipping2', 'container']);
        });
    }
};
