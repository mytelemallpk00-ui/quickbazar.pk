<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table): void {
            $table->id();
            $table->string('session_id', 64)->unique();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 40)->nullable();
            $table->string('status', 32)->default('active');
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chat_id')->constrained('chats')->cascadeOnDelete();
            $table->text('message');
            $table->string('sender_type', 16);
            $table->timestamps();
        });

        Schema::create('chatbot_rules', function (Blueprint $table): void {
            $table->id();
            $table->string('keyword', 120);
            $table->text('response');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('chats');
        Schema::dropIfExists('chatbot_rules');
    }
};
