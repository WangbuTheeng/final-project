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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('subject_type')->nullable(); // Polymorphic relation
            $table->unsignedBigInteger('subject_id')->nullable(); // Polymorphic relation
            $table->string('action'); // create, update, delete, view, login, etc.
            $table->text('description'); // Human-readable description
            $table->json('properties')->nullable(); // Additional data
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->uuid('batch_uuid')->nullable(); // For grouping related activities
            $table->enum('event_type', ['user', 'student', 'teacher', 'course', 'enrollment', 'exam', 'grade', 'system'])->default('system');
            $table->enum('severity', ['low', 'normal', 'high', 'critical'])->default('normal');
            $table->json('tags')->nullable(); // For categorization and filtering
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['user_id', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
            $table->index(['action', 'event_type']);
            $table->index(['severity', 'created_at']);
            $table->index('batch_uuid');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
