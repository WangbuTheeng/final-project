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
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->string('grade_letter', 2); // A+, A, B+, B, C+, C, D+, D, F
            $table->decimal('grade_point', 3, 2); // 4.0, 3.7, 3.3, 3.0, etc.
            $table->decimal('min_percent', 5, 2); // Minimum percentage for this grade
            $table->decimal('max_percent', 5, 2); // Maximum percentage for this grade
            $table->string('description')->nullable(); // Excellent, Very Good, Good, etc.
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('order_sequence')->default(1); // For sorting
            $table->timestamps();

            // Indexes
            $table->index('grade_letter');
            $table->index(['min_percent', 'max_percent']);
            $table->index('status');
            $table->index('order_sequence');

            // Unique constraint
            $table->unique('grade_letter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
    }
};
