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
        if (!Schema::hasTable('grade_scales')) {
            Schema::create('grade_scales', function (Blueprint $table) {
                $table->id();
                $table->string('scale_name')->default('Nepal University Standard');
                $table->string('grade_letter', 5);
                $table->decimal('min_percentage', 5, 2);
                $table->decimal('max_percentage', 5, 2);
                $table->decimal('grade_point', 3, 2);
                $table->string('description');
                $table->enum('status', ['pass', 'fail']);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                // Indexes
                $table->index(['min_percentage', 'max_percentage']);
                $table->index(['grade_letter', 'is_active']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
    }
};
