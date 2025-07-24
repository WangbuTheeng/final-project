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
        // Update the status enum to include more status options
        DB::statement("ALTER TABLE marks MODIFY COLUMN status ENUM('pass', 'fail', 'absent', 'incomplete', 'draft', 'submitted', 'verified') DEFAULT 'pass'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        // First update any new status values to existing ones
        DB::statement("UPDATE marks SET status = 'pass' WHERE status IN ('draft', 'submitted', 'verified')");
        
        // Then change the enum back
        DB::statement("ALTER TABLE marks MODIFY COLUMN status ENUM('pass', 'fail', 'absent', 'incomplete') DEFAULT 'pass'");
    }
};
