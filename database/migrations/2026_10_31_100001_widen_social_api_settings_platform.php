<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('social_api_settings')) {
            return;
        }

        Schema::table('social_api_settings', function (Blueprint $table) {
            $table->string('platform', 64)->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('social_api_settings')) {
            return;
        }

        Schema::table('social_api_settings', function (Blueprint $table) {
            $table->string('platform', 32)->change();
        });
    }
};
