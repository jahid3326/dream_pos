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
        Schema::create('sale_pack_product_items', function (Blueprint $table) {
            $table->id();
            // Link to the parent pack line item in the sale
            $table->foreignId('sale_pack_product_id')->constrained('sale_pack_products')->onDelete('cascade');

            // --- THIS IS THE CORRECTED STRUCTURE ---
            // This is the ID from the 'pack_product' pivot table
            $table->unsignedBigInteger('pack_product_id');
            $table->foreign('pack_product_id', 'sppi_pack_product_foreign')->references('id')->on('pack_product')->onDelete('cascade');

            // This is the ID from the 'pack_product_selected_variations' table
            $table->unsignedBigInteger('pack_product_selected_variation_id');
            $table->foreign('pack_product_selected_variation_id', 'sppi_ppsv_foreign')->references('id')->on('pack_product_selected_variations')->onDelete('cascade');

            // Store historical data for the invoice
            $table->string('product_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_pack_product_items');
    }
};
