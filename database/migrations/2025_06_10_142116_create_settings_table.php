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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'college_name', 'academic_year_start'
            $table->text('value')->nullable(); // Setting value
            $table->string('type')->default('string'); // string, integer, boolean, json, date
            $table->string('group')->default('general'); // general, academic, finance, system
            $table->string('label'); // Human readable label
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // Can be accessed by non-admin users
            $table->boolean('is_editable')->default(true); // Can be modified
            $table->json('validation_rules')->nullable(); // Validation rules for the setting
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['group', 'sort_order']);
            $table->index(['is_public', 'group']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
