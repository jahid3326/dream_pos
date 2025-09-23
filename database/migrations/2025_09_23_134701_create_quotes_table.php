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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->dateTime('quote_date');
            $table->string('status')->default('pending'); // e.g., pending, sent, accepted, rejected
            $table->decimal('sub_total', 12, 2);
            $table->foreignId('order_tax_id')->nullable()->constrained('taxes');
            $table->decimal('order_tax_amount', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->string('discount_type')->nullable();
            $table->decimal('discount_rate', 5, 2)->nullable();
            $table->decimal('shipping', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
