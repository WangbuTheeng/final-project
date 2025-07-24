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
            // Add missing fields for Nepali educational system
            if (!Schema::hasColumn('exams', 'start_time')) {
                $table->time('start_time')->nullable()->after('exam_date');
            }
            if (!Schema::hasColumn('exams', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            if (!Schema::hasColumn('exams', 'max_students')) {
                $table->integer('max_students')->nullable()->after('venue');
            }
            if (!Schema::hasColumn('exams', 'send_notifications')) {
                $table->boolean('send_notifications')->default(true)->after('is_published');
            }
            if (!Schema::hasColumn('exams', 'allow_late_submission')) {
                $table->boolean('allow_late_submission')->default(false)->after('send_notifications');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $columns = ['start_time', 'end_time', 'max_students', 'send_notifications', 'allow_late_submission'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('exams', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
