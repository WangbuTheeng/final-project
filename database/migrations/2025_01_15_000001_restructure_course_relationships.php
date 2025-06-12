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
            // Add faculty_id column
            $table->foreignId('faculty_id')->after('description')->constrained('faculties')->onDelete('cascade');
            
            // Make department_id nullable (optional)
            $table->foreignId('department_id')->nullable()->change();
            
            // Add index for faculty_id
            $table->index('faculty_id');
        });

        // Update existing courses to have faculty_id based on their department's faculty
        DB::statement('
            UPDATE courses 
            SET faculty_id = (
                SELECT faculty_id 
                FROM departments 
                WHERE departments.id = courses.department_id
            )
            WHERE department_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Drop the faculty_id foreign key and column
            $table->dropForeign(['faculty_id']);
            $table->dropColumn('faculty_id');
            
            // Make department_id required again
            $table->foreignId('department_id')->nullable(false)->change();
        });
    }
};
