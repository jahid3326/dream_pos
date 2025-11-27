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
        Schema::create('role_nav_item', function (Blueprint $table) {
            $table->primary(['role_id', 'nav_item_id']);
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('nav_item_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_nav_item');
    }
};
