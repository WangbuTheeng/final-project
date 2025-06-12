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
            // Drop the subject_id foreign key and column
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Add subject_id column back (optional)
            $table->foreignId('subject_id')->nullable()->after('faculty_id')->constrained('subjects')->onDelete('set null');
            
            // Add index for subject_id
            $table->index('subject_id');
        });
    }
};
