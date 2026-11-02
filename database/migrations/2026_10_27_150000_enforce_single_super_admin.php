<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'is_super_admin')) {
            return;
        }

        $superIds = DB::table('users')
            ->where('is_super_admin', true)
            ->orderBy('id')
            ->pluck('id');

        if ($superIds->count() <= 1) {
            return;
        }

        $keepId = $superIds->first();

        DB::table('users')
            ->whereIn('id', $superIds->slice(1)->all())
            ->update([
                'is_super_admin' => false,
            ]);
    }

    public function down(): void
    {
        // Irreversible data normalization.
    }
};
