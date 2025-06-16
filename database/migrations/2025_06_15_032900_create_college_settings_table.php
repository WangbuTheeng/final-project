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
        Schema::create('college_settings', function (Blueprint $table) {
            $table->id();
            $table->string('college_name');
            $table->text('college_address');
            $table->string('college_phone')->nullable();
            $table->string('college_email')->nullable();
            $table->string('college_website')->nullable();
            $table->string('logo_path')->nullable();
            $table->text('result_header')->nullable(); // Header text for marksheets
            $table->text('result_footer')->nullable(); // Footer text for marksheets
            $table->string('principal_name')->nullable();
            $table->string('principal_signature_path')->nullable();
            $table->string('exam_controller_name')->nullable();
            $table->string('exam_controller_signature_path')->nullable();
            $table->string('registrar_name')->nullable();
            $table->string('registrar_signature_path')->nullable();
            $table->string('class_teacher_name')->nullable();
            $table->string('class_teacher_signature_path')->nullable();
            $table->string('hod_name')->nullable();
            $table->string('hod_signature_path')->nullable();
            $table->json('marksheet_settings')->nullable(); // Additional marksheet configurations
            $table->enum('grading_system', ['percentage', 'gpa', 'both'])->default('both');
            $table->decimal('pass_percentage', 5, 2)->default(40.00);
            $table->boolean('show_grade_points')->default(true);
            $table->boolean('show_percentage')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('college_settings');
    }
};
