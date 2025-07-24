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
        // Add course_id column if it doesn't exist
        if (!Schema::hasColumn('fees', 'course_id')) {
            Schema::table('fees', function (Blueprint $table) {
                $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade')->after('amount');
            });
        }

        // Drop old indexes if they exist (skip if they don't exist or are used by foreign keys)
        try {
            Schema::table('fees', function (Blueprint $table) {
                if (Schema::hasColumn('fees', 'semester')) {
                    $table->dropIndex('fees_academic_year_id_semester_is_active_index');
                }
            });
        } catch (\Exception $e) {
            // Index might not exist or be used by foreign key, continue
        }

        try {
            Schema::table('fees', function (Blueprint $table) {
                if (Schema::hasColumn('fees', 'level')) {
                    $table->dropIndex('fees_department_id_level_index');
                }
            });
        } catch (\Exception $e) {
            // Index might not exist, continue
        }

        // Remove old columns if they exist
        Schema::table('fees', function (Blueprint $table) {
            if (Schema::hasColumn('fees', 'semester')) {
                $table->dropColumn('semester');
            }
            if (Schema::hasColumn('fees', 'study_mode')) {
                $table->dropColumn('study_mode');
            }
            if (Schema::hasColumn('fees', 'level')) {
                $table->dropColumn('level');
            }
        });

        // Add new indexes (skip if they already exist)
        try {
            Schema::table('fees', function (Blueprint $table) {
                $table->index(['academic_year_id', 'is_active']);
            });
        } catch (\Exception $e) {
            // Index might already exist, continue
        }

        try {
            Schema::table('fees', function (Blueprint $table) {
                $table->index(['course_id', 'department_id']);
            });
        } catch (\Exception $e) {
            // Index might already exist, continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new indexes safely
        try {
            Schema::table('fees', function (Blueprint $table) {
                $table->dropIndex('fees_academic_year_id_is_active_index');
            });
        } catch (\Exception $e) {
            // Index might not exist or be used by foreign key, continue
        }

        try {
            Schema::table('fees', function (Blueprint $table) {
                $table->dropIndex('fees_course_id_department_id_index');
            });
        } catch (\Exception $e) {
            // Index might not exist, continue
        }

        // Remove course_id column
        try {
            Schema::table('fees', function (Blueprint $table) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            });
        } catch (\Exception $e) {
            // Foreign key or column might not exist, continue
        }

        // Add back old columns
        try {
            Schema::table('fees', function (Blueprint $table) {
                if (!Schema::hasColumn('fees', 'level')) {
                    $table->integer('level')->nullable()->after('department_id');
                }
                if (!Schema::hasColumn('fees', 'study_mode')) {
                    $table->enum('study_mode', ['full_time', 'part_time', 'distance', 'all'])->default('all')->after('level');
                }
                if (!Schema::hasColumn('fees', 'semester')) {
                    $table->enum('semester', ['first', 'second', 'both'])->default('both')->after('academic_year_id');
                }
            });
        } catch (\Exception $e) {
            // Columns might already exist, continue
        }

        // Restore old indexes
        try {
            Schema::table('fees', function (Blueprint $table) {
                $table->index(['academic_year_id', 'semester', 'is_active']);
                $table->index(['department_id', 'level']);
            });
        } catch (\Exception $e) {
            // Indexes might already exist, continue
        }
    }
};
