<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_super_admin')) {
                $table->boolean('is_super_admin')->default(false)->after('is_admin');
            }
            if (! Schema::hasColumn('users', 'admin_permissions')) {
                $table->json('admin_permissions')->nullable()->after('is_super_admin');
            }
        });

        if (Schema::hasColumn('users', 'is_super_admin')) {
            $superId = DB::table('users')
                ->where('is_admin', true)
                ->orderBy('id')
                ->value('id');

            if ($superId) {
                DB::table('users')
                    ->where('is_admin', true)
                    ->update(['is_super_admin' => false]);

                DB::table('users')
                    ->where('id', $superId)
                    ->update(['is_super_admin' => true, 'admin_permissions' => null]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            foreach (['admin_permissions', 'is_super_admin'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
