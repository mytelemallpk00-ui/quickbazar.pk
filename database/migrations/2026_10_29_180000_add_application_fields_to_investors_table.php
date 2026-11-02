<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('investors')) {
            return;
        }

        Schema::table('investors', function (Blueprint $table) {
            if (! Schema::hasColumn('investors', 'location')) {
                $table->string('location', 255)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('investors', 'investor_type')) {
                $table->string('investor_type', 32)->nullable()->after('location');
            }
            if (! Schema::hasColumn('investors', 'estimated_investment_range')) {
                $table->string('estimated_investment_range', 64)->nullable()->after('investor_type');
            }
            if (! Schema::hasColumn('investors', 'company_name')) {
                $table->string('company_name', 255)->nullable()->after('estimated_investment_range');
            }
            if (! Schema::hasColumn('investors', 'linkedin_url')) {
                $table->string('linkedin_url', 512)->nullable()->after('company_name');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('investors')) {
            return;
        }

        Schema::table('investors', function (Blueprint $table) {
            foreach (['linkedin_url', 'company_name', 'estimated_investment_range', 'investor_type', 'location'] as $column) {
                if (Schema::hasColumn('investors', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
