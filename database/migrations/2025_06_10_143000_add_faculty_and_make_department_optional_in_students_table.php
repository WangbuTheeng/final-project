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
        Schema::table('students', function (Blueprint $table) {
            // Add faculty_id field
            $table->foreignId('faculty_id')->nullable()->after('department_id')->constrained('faculties')->onDelete('set null');
        });

        // Update the foreign key constraint for department_id to set null on delete and make it nullable
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->bigInteger('department_id')->unsigned()->nullable()->change();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop faculty_id field
            $table->dropForeign(['faculty_id']);
            $table->dropColumn('faculty_id');
        });

        // Restore original foreign key constraint for department_id and make it required
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->bigInteger('department_id')->unsigned()->nullable(false)->change();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }
};
