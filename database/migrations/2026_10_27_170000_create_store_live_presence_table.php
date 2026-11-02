<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('store_live_presence')) {
            return;
        }

        Schema::create('store_live_presence', function (Blueprint $table) {
            $table->string('session_id', 128)->primary();
            $table->timestamp('last_seen_at');
            $table->timestamps();
            $table->index('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_live_presence');
    }
};
