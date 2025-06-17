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
            // Add grading system relationship
            $table->foreignId('grading_system_id')->nullable()->after('id')->constrained('grading_systems')->onDelete('cascade');
            
            // Add index for better performance
            $table->index(['grading_system_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_scales', function (Blueprint $table) {
            $table->dropForeign(['grading_system_id']);
            $table->dropIndex(['grading_system_id', 'status']);
            $table->dropColumn('grading_system_id');
        });
    }
};
