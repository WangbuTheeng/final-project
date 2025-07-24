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
        Schema::table('users', function (Blueprint $table) {
            // Add alternative identification fields for students without citizenship numbers
            $table->string('alternative_id_type')->nullable()->after('citizenship_number')
                  ->comment('Type of alternative ID: passport, national_id, driving_license, student_visa, other');
            $table->string('alternative_id_number')->nullable()->after('alternative_id_type')
                  ->comment('Alternative ID number for students without citizenship number');
            
            // Add index for alternative ID lookup
            $table->index(['alternative_id_type', 'alternative_id_number'], 'users_alternative_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_alternative_id_index');
            $table->dropColumn(['alternative_id_type', 'alternative_id_number']);
        });
    }
};
