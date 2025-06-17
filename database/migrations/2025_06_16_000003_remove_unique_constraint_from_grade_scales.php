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
        Schema::table('grade_scales', function (Blueprint $table) {
            // Drop the unique constraint on grade_letter
            $table->dropUnique(['grade_letter']);
            
            // Add a composite unique constraint for grade_letter within a grading system
            $table->unique(['grading_system_id', 'grade_letter'], 'grade_scales_system_letter_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_scales', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('grade_scales_system_letter_unique');
            
            // Add back the unique constraint on grade_letter
            $table->unique('grade_letter');
        });
    }
};
