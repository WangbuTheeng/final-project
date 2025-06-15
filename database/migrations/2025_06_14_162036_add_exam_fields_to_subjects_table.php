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
        Schema::table('subjects', function (Blueprint $table) {
            // Add theory/practical marks fields for exam management
            if (!Schema::hasColumn('subjects', 'full_marks_theory')) {
                $table->integer('full_marks_theory')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('subjects', 'pass_marks_theory')) {
                $table->integer('pass_marks_theory')->nullable()->after('full_marks_theory');
            }
            if (!Schema::hasColumn('subjects', 'full_marks_practical')) {
                $table->integer('full_marks_practical')->nullable()->after('pass_marks_theory');
            }
            if (!Schema::hasColumn('subjects', 'pass_marks_practical')) {
                $table->integer('pass_marks_practical')->nullable()->after('full_marks_practical');
            }
            if (!Schema::hasColumn('subjects', 'is_practical')) {
                $table->boolean('is_practical')->default(false)->after('pass_marks_practical');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $columns = ['full_marks_theory', 'pass_marks_theory', 'full_marks_practical', 'pass_marks_practical', 'is_practical'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('subjects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
