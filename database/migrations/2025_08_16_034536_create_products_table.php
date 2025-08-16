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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->enum('type', ['single', 'variation']);

            // Fields ONLY for 'single' type products (nullable)
            $table->string('sku')->nullable()->unique();
            $table->string('measurement')->nullable();
            $table->decimal('cbm', 8, 4)->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->unsignedBigInteger('tax_id')->nullable(); // Assuming a future 'taxes' table
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
