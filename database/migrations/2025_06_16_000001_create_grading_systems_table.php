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
        Schema::create('grading_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "TU Grading System", "CBSE Grading", "Custom Grading"
            $table->string('code')->unique(); // e.g., "TU", "CBSE", "CUSTOM"
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_default')->default(false);
            $table->integer('order_sequence')->default(1);
            $table->timestamps();

            // Indexes
            $table->index(['status', 'is_default']);
            $table->index('order_sequence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grading_systems');
    }
};
