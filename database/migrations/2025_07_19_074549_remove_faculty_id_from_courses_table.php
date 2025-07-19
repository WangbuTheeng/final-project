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
        Schema::table('courses', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['faculty_id']);
            // Then drop the column
            $table->dropColumn('faculty_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Add the column back
            $table->foreignId('faculty_id')->constrained('faculties')->onDelete('cascade');
        });
    }
};