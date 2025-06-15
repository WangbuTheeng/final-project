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
        Schema::table('exams', function (Blueprint $table) {
            // Add missing year column
            if (!Schema::hasColumn('exams', 'year')) {
                $table->integer('year')->nullable()->after('semester');
            }

            // Add missing theory_marks column
            if (!Schema::hasColumn('exams', 'theory_marks')) {
                $table->decimal('theory_marks', 8, 2)->nullable()->after('total_marks');
            }

            // Add missing practical_marks column
            if (!Schema::hasColumn('exams', 'practical_marks')) {
                $table->decimal('practical_marks', 8, 2)->nullable()->after('theory_marks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $columns = ['year', 'theory_marks', 'practical_marks'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('exams', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
