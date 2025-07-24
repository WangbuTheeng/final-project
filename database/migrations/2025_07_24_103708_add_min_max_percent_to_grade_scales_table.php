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
        Schema::table('grade_scales', function (Blueprint $table) {
            // Add min_percent and max_percent columns for view compatibility
            if (!Schema::hasColumn('grade_scales', 'min_percent')) {
                $table->decimal('min_percent', 5, 2)->nullable()->after('max_percentage');
            }
            if (!Schema::hasColumn('grade_scales', 'max_percent')) {
                $table->decimal('max_percent', 5, 2)->nullable()->after('min_percent');
            }
        });

        // Update existing records to populate the new columns only if source columns exist
        if (Schema::hasColumn('grade_scales', 'min_percentage') && Schema::hasColumn('grade_scales', 'max_percentage')) {
            try {
                DB::statement('UPDATE grade_scales SET min_percent = min_percentage, max_percent = max_percentage WHERE min_percent IS NULL OR max_percent IS NULL');
            } catch (\Exception $e) {
                // If the update fails, continue - the columns might not have data or structure might be different
                \Log::warning('Could not update grade_scales min/max percent columns: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_scales', function (Blueprint $table) {
            if (Schema::hasColumn('grade_scales', 'min_percent')) {
                $table->dropColumn('min_percent');
            }
            if (Schema::hasColumn('grade_scales', 'max_percent')) {
                $table->dropColumn('max_percent');
            }
        });
    }
};
