<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category')->nullable();
            $table->string('read_time')->nullable();
            $table->date('published_at')->nullable();
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->string('author_role')->nullable();
            $table->string('author_avatar')->nullable();
            $table->unsignedInteger('likes')->default(0);
            $table->text('snippet')->nullable();
            $table->longText('content');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
