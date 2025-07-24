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
        Schema::table('exams', function (Blueprint $table) {
            // Make sure exam_type_id column exists and is properly set up
            if (!Schema::hasColumn('exams', 'exam_type_id')) {
                $table->unsignedBigInteger('exam_type_id')->nullable()->after('exam_type');
                $table->foreign('exam_type_id')->references('id')->on('exam_types')->onDelete('set null');
            }
        });

        // Update existing exams to have exam_type_id based on their exam_type
        // First, ensure we have some default exam types
        $examTypes = [
            'first_assessment' => 'First Assessment',
            'first_terminal' => 'First Terminal',
            'second_assessment' => 'Second Assessment',
            'second_terminal' => 'Second Terminal',
            'third_assessment' => 'Third Assessment',
            'final_term' => 'Final Term',
            'monthly_term' => 'Monthly Term',
            'weekly_test' => 'Weekly Test'
        ];

        foreach ($examTypes as $key => $name) {
            DB::table('exam_types')->updateOrInsert(
                ['name' => $name],
                [
                    'name' => $name,
                    'code' => $key,
                    'is_active' => true,
                    'order_sequence' => array_search($key, array_keys($examTypes)) + 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        // Update existing exams to link to exam_type_id
        foreach ($examTypes as $key => $name) {
            $examType = DB::table('exam_types')->where('name', $name)->first();
            if ($examType) {
                DB::table('exams')
                    ->where('exam_type', $key)
                    ->update(['exam_type_id' => $examType->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original status enum
        DB::statement("ALTER TABLE exams MODIFY COLUMN status ENUM('scheduled', 'ongoing', 'completed', 'cancelled') DEFAULT 'scheduled'");
        
        // Revert to original exam_type enum
        DB::statement("ALTER TABLE exams MODIFY COLUMN exam_type ENUM('internal', 'board', 'practical', 'midterm', 'annual', 'quiz', 'test', 'final', 'assignment') DEFAULT 'internal'");
    }
};
