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
        Schema::table('students', function (Blueprint $table) {
            // Remove current_level and study_mode columns
            $table->dropColumn(['current_level', 'study_mode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Add back the columns if migration is rolled back
            $table->integer('current_level')->default(100)->after('academic_year_id');
            $table->enum('study_mode', ['full_time', 'part_time', 'distance'])->default('full_time')->after('mode_of_entry');
        });
    }
};
