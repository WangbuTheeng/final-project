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
        // Update the status enum to include 'published'
        DB::statement("ALTER TABLE exams MODIFY COLUMN status ENUM('scheduled', 'ongoing', 'completed', 'cancelled', 'published') DEFAULT 'scheduled'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update any 'published' status to 'scheduled' before removing the enum value
        DB::statement("UPDATE exams SET status = 'scheduled' WHERE status = 'published'");

        // Revert the status enum to original values
        DB::statement("ALTER TABLE exams MODIFY COLUMN status ENUM('scheduled', 'ongoing', 'completed', 'cancelled') DEFAULT 'scheduled'");
    }
};
