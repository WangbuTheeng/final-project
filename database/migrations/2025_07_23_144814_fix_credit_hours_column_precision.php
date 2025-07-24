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
        Schema::table('enrollments', function (Blueprint $table) {
            // Modify credit_hours column to allow larger values (up to 9999.99)
            $table->decimal('credit_hours', 6, 2)->nullable()->change();
        });

        // Also fix the subjects table credit_hours column if it exists
        if (Schema::hasColumn('subjects', 'credit_hours')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->decimal('credit_hours', 6, 2)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Revert back to original precision (this may cause data loss if values > 99.99 exist)
            $table->decimal('credit_hours', 4, 2)->nullable()->change();
        });

        if (Schema::hasColumn('subjects', 'credit_hours')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->decimal('credit_hours', 4, 2)->nullable()->change();
            });
        }
    }
};
