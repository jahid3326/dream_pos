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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            // We'll create a User record for each customer for login purposes
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Customer-specific details
            $table->string('company_name')->nullable();
            $table->string('tax_number')->nullable();
            $table->text('billing_address')->nullable();
            $table->boolean('status')->default(true); // true = Enabled, false = Disabled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
