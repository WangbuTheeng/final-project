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
        Schema::table('classes', function (Blueprint $table) {
            // Add year column for year-based courses
            $table->integer('year')->nullable()->after('semester');

            // Make semester nullable to support year-based courses
            $table->integer('semester')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Remove year column
            $table->dropColumn('year');

            // Make semester non-nullable again
            $table->integer('semester')->nullable(false)->change();
        });
    }
};
