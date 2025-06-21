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
        Schema::table('salary_payments', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('status');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('notes');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_payments', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['notes', 'approved_by', 'approved_at']);
        });
    }
};
