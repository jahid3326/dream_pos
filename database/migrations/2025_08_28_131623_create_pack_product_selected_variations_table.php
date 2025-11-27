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
        Schema::create('pack_product_selected_variations', function (Blueprint $table) {
            $table->id();
            // This links to the entry in the main pack_product pivot table.
            // It identifies WHICH item in the pack list these selections belong to.
            $table->unsignedBigInteger('pack_product_id');
            $table->foreign('pack_product_id', 'ppsv_pack_product_foreign')
                ->references('id')->on('pack_product')->onDelete('cascade');

            // --- THIS IS THE CORRECTED PART ---

            // This foreign key will hold the ID of a 'single' type product.
            // It MUST be nullable because if an item is a variation, this will be NULL.
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');

            // This foreign key will hold the ID of a 'variation'.
            // It MUST be nullable because if an item is a single product, this will be NULL.
            $table->foreignId('product_variation_id')->nullable()->constrained('product_variations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pack_product_selected_variations');
    }
};
