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
        // First, update existing data to convert text semesters to numbers in classes table
        DB::statement("UPDATE classes SET semester = CASE
            WHEN semester = 'first' THEN '1'
            WHEN semester = 'second' THEN '2'
            ELSE semester
        END");

        // Now modify the classes column to accept integers 1-8
        Schema::table('classes', function (Blueprint $table) {
            $table->integer('semester')->change();
        });

        // Update existing data to convert text semesters to numbers in enrollments table
        DB::statement("UPDATE enrollments SET semester = CASE
            WHEN semester = 'first' THEN '1'
            WHEN semester = 'second' THEN '2'
            ELSE semester
        END");

        // Now modify the enrollments column to accept integers 1-8
        Schema::table('enrollments', function (Blueprint $table) {
            $table->integer('semester')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to the original enum for classes
        DB::statement("UPDATE classes SET semester = CASE
            WHEN semester = '1' THEN 'first'
            WHEN semester = '2' THEN 'second'
            ELSE 'first'
        END");

        Schema::table('classes', function (Blueprint $table) {
            $table->enum('semester', ['first', 'second'])->change();
        });

        // Convert back to the original enum for enrollments
        DB::statement("UPDATE enrollments SET semester = CASE
            WHEN semester = '1' THEN 'first'
            WHEN semester = '2' THEN 'second'
            ELSE 'first'
        END");

        Schema::table('enrollments', function (Blueprint $table) {
            $table->enum('semester', ['first', 'second'])->change();
        });
    }
};
