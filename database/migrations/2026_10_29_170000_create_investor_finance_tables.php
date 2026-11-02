<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 32)->nullable();
            $table->string('status', 32)->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->date('investment_date');
            $table->string('status', 32)->default('pending');
            $table->string('payment_proof')->nullable();
            $table->string('payment_method', 64)->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['investor_id', 'status']);
            $table->index('investment_date');
        });

        Schema::create('profit_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->date('distribution_date');
            $table->text('notes')->nullable();
            $table->string('status', 32)->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->decimal('share_percent', 10, 4)->nullable();
            $table->foreignId('marked_paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['investor_id', 'status']);
            $table->index('distribution_date');
        });

        Schema::create('investor_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained()->cascadeOnDelete();
            $table->string('direction', 16);
            $table->string('category', 32);
            $table->decimal('amount', 14, 2);
            $table->string('currency', 3)->default('PKR');
            $table->string('status', 32)->default('completed');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('occurred_at');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['investor_id', 'occurred_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investor_transactions');
        Schema::dropIfExists('profit_distributions');
        Schema::dropIfExists('investments');
        Schema::dropIfExists('investors');
    }
};
