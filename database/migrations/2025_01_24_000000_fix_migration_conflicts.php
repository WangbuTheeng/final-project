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
        // Fix grade_scales table if it exists but migration is not recorded
        if (Schema::hasTable('grade_scales')) {
            // Check if migration is recorded
            $migrationExists = DB::table('migrations')
                ->where('migration', '2024_01_20_000003_create_grade_scales_table')
                ->exists();
            
            if (!$migrationExists) {
                // Mark the migration as run
                DB::table('migrations')->insert([
                    'migration' => '2024_01_20_000003_create_grade_scales_table',
                    'batch' => DB::table('migrations')->max('batch') + 1
                ]);
            }
        }

        // Fix fees table foreign key issues
        if (Schema::hasTable('fees')) {
            // Drop problematic indexes that might conflict with foreign keys
            try {
                Schema::table('fees', function (Blueprint $table) {
                    // Check if index exists before dropping
                    $indexes = DB::select("SHOW INDEX FROM fees WHERE Key_name = 'fees_academic_year_id_is_active_index'");
                    if (!empty($indexes)) {
                        $table->dropIndex('fees_academic_year_id_is_active_index');
                    }
                });
            } catch (\Exception $e) {
                // Index might not exist or be used by foreign key, continue
            }

            try {
                Schema::table('fees', function (Blueprint $table) {
                    $indexes = DB::select("SHOW INDEX FROM fees WHERE Key_name = 'fees_course_id_department_id_index'");
                    if (!empty($indexes)) {
                        $table->dropIndex('fees_course_id_department_id_index');
                    }
                });
            } catch (\Exception $e) {
                // Index might not exist, continue
            }
        }

        // Fix exam_types table if it exists but migration is not recorded
        if (Schema::hasTable('exam_types')) {
            $migrationExists = DB::table('migrations')
                ->where('migration', '2025_07_23_000001_create_exam_types_table')
                ->exists();

            if (!$migrationExists) {
                DB::table('migrations')->insert([
                    'migration' => '2025_07_23_000001_create_exam_types_table',
                    'batch' => DB::table('migrations')->max('batch') + 1
                ]);
            }
        }

        // Ensure all required tables exist with proper structure
        $this->ensureGradeScalesTable();
        $this->ensureGradingSystemsTable();
        $this->ensureCollegeSettingsTable();
        $this->ensureExamTypesTable();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the migration record we added
        DB::table('migrations')
            ->where('migration', '2024_01_20_000003_create_grade_scales_table')
            ->delete();
    }

    /**
     * Ensure grade_scales table exists with proper structure
     */
    private function ensureGradeScalesTable(): void
    {
        if (!Schema::hasTable('grade_scales')) {
            Schema::create('grade_scales', function (Blueprint $table) {
                $table->id();
                $table->string('scale_name')->default('Nepal University Standard');
                $table->string('grade_letter', 5);
                $table->decimal('min_percentage', 5, 2);
                $table->decimal('max_percentage', 5, 2);
                $table->decimal('grade_point', 3, 2);
                $table->string('description');
                $table->enum('status', ['pass', 'fail']);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                
                // Indexes
                $table->index(['min_percentage', 'max_percentage']);
                $table->index(['grade_letter', 'is_active']);
            });
        }
    }

    /**
     * Ensure grading_systems table exists
     */
    private function ensureGradingSystemsTable(): void
    {
        if (!Schema::hasTable('grading_systems')) {
            Schema::create('grading_systems', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Ensure college_settings table has required columns
     */
    private function ensureCollegeSettingsTable(): void
    {
        if (Schema::hasTable('college_settings')) {
            // Add missing columns if they don't exist
            Schema::table('college_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('college_settings', 'logo_path')) {
                    $table->string('logo_path')->nullable()->after('website');
                }
                if (!Schema::hasColumn('college_settings', 'result_footer')) {
                    $table->text('result_footer')->nullable();
                }
                if (!Schema::hasColumn('college_settings', 'show_college_logo')) {
                    $table->boolean('show_college_logo')->default(true);
                }
            });
        }
    }

    /**
     * Ensure exam_types table exists
     */
    private function ensureExamTypesTable(): void
    {
        if (!Schema::hasTable('exam_types')) {
            Schema::create('exam_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->enum('education_level', ['school', 'college', 'both'])->default('both');
                $table->enum('assessment_category', ['internal', 'external', 'both'])->default('internal');
                $table->decimal('default_weightage', 5, 2)->default(0);
                $table->integer('default_duration_minutes')->default(60);
                $table->boolean('is_active')->default(true);
                $table->integer('order_sequence')->default(0);
                $table->timestamps();

                $table->index(['education_level', 'is_active']);
                $table->index(['assessment_category', 'is_active']);
            });
        }
    }
};
