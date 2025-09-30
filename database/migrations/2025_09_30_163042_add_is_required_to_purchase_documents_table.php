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
        Schema::table('purchase_documents', function (Blueprint $table) {
            $table->boolean('is_required')->default(false)->after('document_name');
            $table->string('status')->default('pending')->after('is_required'); // e.g., pending, uploaded, approved
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_documents', function (Blueprint $table) {
            $table->dropColumn(['is_required', 'status']);
        });
    }
};
