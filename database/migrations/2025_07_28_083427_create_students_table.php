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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            // Foreign key to the users table.
            // `onDelete('cascade')` means if the user is deleted, this student record is also deleted.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Student-specific information
            $table->string('roll_number')->unique();
            $table->string('class_name'); // e.g., "Grade 10 - A"
            $table->string('parent_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
