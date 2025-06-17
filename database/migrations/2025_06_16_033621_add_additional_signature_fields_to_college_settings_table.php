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
        Schema::table('college_settings', function (Blueprint $table) {
            // Add columns only if they don't exist
            if (!Schema::hasColumn('college_settings', 'class_teacher_name')) {
                $table->string('class_teacher_name')->nullable()->after('registrar_signature_path');
            }
            if (!Schema::hasColumn('college_settings', 'class_teacher_signature_path')) {
                $table->string('class_teacher_signature_path')->nullable()->after('class_teacher_name');
            }
            if (!Schema::hasColumn('college_settings', 'hod_name')) {
                $table->string('hod_name')->nullable()->after('class_teacher_signature_path');
            }
            if (!Schema::hasColumn('college_settings', 'hod_signature_path')) {
                $table->string('hod_signature_path')->nullable()->after('hod_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('college_settings', function (Blueprint $table) {
            // Only drop columns that exist
            $columnsToCheck = ['class_teacher_name', 'class_teacher_signature_path', 'hod_name', 'hod_signature_path'];
            $columnsToDrop = [];

            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('college_settings', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
