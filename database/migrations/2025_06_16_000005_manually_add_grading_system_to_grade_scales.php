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
        // Check if the column doesn't exist before adding it
        if (!Schema::hasColumn('grade_scales', 'grading_system_id')) {
            Schema::table('grade_scales', function (Blueprint $table) {
                $table->foreignId('grading_system_id')->nullable()->after('id')->constrained('grading_systems')->onDelete('cascade');
                $table->index(['grading_system_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('grade_scales', 'grading_system_id')) {
            Schema::table('grade_scales', function (Blueprint $table) {
                $table->dropForeign(['grading_system_id']);
                $table->dropIndex(['grading_system_id', 'status']);
                $table->dropColumn('grading_system_id');
            });
        }
    }
};
