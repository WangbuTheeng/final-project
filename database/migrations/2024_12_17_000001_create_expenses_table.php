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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', [
                'utilities',
                'maintenance',
                'supplies',
                'equipment',
                'travel',
                'training',
                'marketing',
                'insurance',
                'rent',
                'food',
                'transportation',
                'communication',
                'professional_services',
                'other'
            ]);
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'cheque',
                'card',
                'online',
                'other'
            ]);
            $table->string('vendor_name')->nullable();
            $table->string('vendor_contact')->nullable();
            $table->string('receipt_number')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['status', 'expense_date']);
            $table->index(['category', 'expense_date']);
            $table->index(['department_id', 'expense_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
