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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // e.g., "CSC", "EEE", "CVE"
            $table->text('description')->nullable();
            $table->foreignId('faculty_id')->constrained('faculties')->onDelete('cascade');
            $table->foreignId('hod_id')->nullable()->constrained('users')->onDelete('set null'); // Head of Department
            $table->string('location')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('duration_years')->default(4); // Program duration
            $table->enum('degree_type', ['bachelor', 'master', 'phd', 'diploma', 'certificate'])->default('bachelor');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['faculty_id', 'is_active']);
            $table->index('hod_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
