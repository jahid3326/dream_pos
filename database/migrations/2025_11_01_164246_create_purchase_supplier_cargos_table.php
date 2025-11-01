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
        Schema::create('purchase_supplier_cargos', function (Blueprint $table) {
            $table->id();
            // Link to a specific supplier on a specific purchase
            $table->unsignedBigInteger('purchase_supplier_id')->unique();
            $table->foreign('purchase_supplier_id', 'cargo_ps_foreign')->references('id')->on('purchase_supplier')->onDelete('cascade');

            $table->string('packing_type')->nullable(); // Wood Frame, Pallet, etc.
            $table->decimal('gross_weight', 10, 2)->nullable();
            $table->decimal('total_cbm', 10, 4)->nullable();
            $table->integer('quantity')->nullable(); // Total number of packages/pallets
            $table->boolean('hazardous_materials')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_supplier_cargos');
    }
};
