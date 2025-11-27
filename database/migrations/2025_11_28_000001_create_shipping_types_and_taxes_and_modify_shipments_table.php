<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates `shipping_types` and `shipping_taxes` tables
     * and replaces the `type_shipping1` and `type_shipping2` string columns
     * on `shipments` with nullable foreign keys `shipping_type_id` and `shipping_tax_id`.
     *
     * Note: If your production DB contains values in the old string columns
     * you should implement a data migration before dropping them.
     */
    public function up(): void
    {
        Schema::create('shipping_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('shipping_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('shipments', function (Blueprint $table) {
            // add new nullable FK columns
            $table->unsignedBigInteger('shipping_type_id')->nullable()->after('total_amount');
            $table->unsignedBigInteger('shipping_tax_id')->nullable()->after('shipping_type_id');

            $table->foreign('shipping_type_id')->references('id')->on('shipping_types')->onDelete('set null');
            $table->foreign('shipping_tax_id')->references('id')->on('shipping_taxes')->onDelete('set null');

            // Drop old string columns if they exist
            if (Schema::hasColumn('shipments', 'type_shipping1')) {
                $table->dropColumn('type_shipping1');
            }
            if (Schema::hasColumn('shipments', 'type_shipping2')) {
                $table->dropColumn('type_shipping2');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // restore old columns
            if (!Schema::hasColumn('shipments', 'type_shipping1')) {
                $table->string('type_shipping1')->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('shipments', 'type_shipping2')) {
                $table->string('type_shipping2')->nullable()->after('type_shipping1');
            }

            // drop foreign keys and columns
            if (Schema::hasColumn('shipments', 'shipping_type_id')) {
                $table->dropForeign(['shipping_type_id']);
                $table->dropColumn('shipping_type_id');
            }
            if (Schema::hasColumn('shipments', 'shipping_tax_id')) {
                $table->dropForeign(['shipping_tax_id']);
                $table->dropColumn('shipping_tax_id');
            }
        });

        Schema::dropIfExists('shipping_taxes');
        Schema::dropIfExists('shipping_types');
    }
};
