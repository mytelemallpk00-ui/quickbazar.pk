<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_api_settings', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 64)->unique();
            $table->json('credentials')->nullable();
            $table->timestamps();
        });

        Schema::create('scheduled_posts', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->json('platforms');
            $table->string('title')->nullable();
            $table->text('caption');
            $table->string('media_path')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('scheduled_at')->index();
            $table->text('error_log')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_posts');
        Schema::dropIfExists('social_api_settings');
    }
};
