<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update existing data to match new format
        DB::statement("UPDATE exams SET semester = '1' WHERE semester = 'first'");
        DB::statement("UPDATE exams SET semester = '2' WHERE semester = 'second'");
        
        // Change semester column from enum to integer
        DB::statement("ALTER TABLE exams MODIFY COLUMN semester INT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to enum
        DB::statement("ALTER TABLE exams MODIFY COLUMN semester ENUM('first','second') NOT NULL");
        
        // Convert data back
        DB::statement("UPDATE exams SET semester = 'first' WHERE semester = '1'");
        DB::statement("UPDATE exams SET semester = 'second' WHERE semester = '2'");
    }
};
