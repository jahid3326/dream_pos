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
        Schema::table('cargo_dimensions', function (Blueprint $table) {
            $table->integer('length')->unsigned()->change();
            $table->integer('width')->unsigned()->change();
            $table->integer('height')->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cargo_dimensions', function (Blueprint $table) {
            $table->decimal('length', 8, 2)->change();
            $table->decimal('width', 8, 2)->change();
            $table->decimal('height', 8, 2)->change();
        });
    }
};
