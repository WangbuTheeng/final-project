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
        Schema::table('grade_scales', function (Blueprint $table) {
            // Add order_sequence column if it doesn't exist
            if (!Schema::hasColumn('grade_scales', 'order_sequence')) {
                $table->integer('order_sequence')->default(0)->after('sort_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_scales', function (Blueprint $table) {
            if (Schema::hasColumn('grade_scales', 'order_sequence')) {
                $table->dropColumn('order_sequence');
            }
        });
    }
};
