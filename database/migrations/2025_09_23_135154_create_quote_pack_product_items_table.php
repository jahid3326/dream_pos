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
        Schema::create('quote_pack_product_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_pack_product_id')->constrained('quote_pack_products')->onDelete('cascade');
            $table->unsignedBigInteger('pack_product_id');
            $table->foreign('pack_product_id', 'qppi_pack_product_foreign')->references('id')->on('pack_product')->onDelete('cascade');
            $table->unsignedBigInteger('pack_product_selected_variation_id');
            $table->foreign('pack_product_selected_variation_id', 'qppi_ppsv_foreign')->references('id')->on('pack_product_selected_variations')->onDelete('cascade');
            $table->string('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_pack_product_items');
    }
};
