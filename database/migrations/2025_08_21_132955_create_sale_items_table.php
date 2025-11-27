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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('product_variation_id')->nullable()->constrained('product_variations');

            $table->string('product_name'); // Store name at time of sale
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2); // Store price at time of sale
            $table->decimal('item_tax_percent', 5, 2)->default(0);
            $table->decimal('item_tax_amount', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2); // This is the total including item tax
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
