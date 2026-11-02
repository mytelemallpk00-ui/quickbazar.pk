<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_index_logs', function (Blueprint $table) {
            $table->id();
            $table->string('url', 2048);
            $table->string('action', 32)->default('URL_UPDATED');
            $table->string('status', 32);
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->text('message')->nullable();
            $table->json('response_body')->nullable();
            $table->nullableMorphs('indexable');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('url');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_index_logs');
    }
};
