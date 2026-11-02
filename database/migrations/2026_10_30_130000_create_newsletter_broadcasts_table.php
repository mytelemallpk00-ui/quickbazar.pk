<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_broadcasts', function (Blueprint $table): void {
            $table->id();
            $table->string('list', 32);
            $table->string('subject');
            $table->string('preheader')->nullable();
            $table->string('headline')->nullable();
            $table->longText('body_html')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedInteger('recipients_count')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->string('status', 24)->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['list', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_broadcasts');
    }
};
