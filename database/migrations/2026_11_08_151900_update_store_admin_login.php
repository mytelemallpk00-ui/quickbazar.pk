<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $now = now();
        $values = [
            'name' => 'Quick Bazar Admin',
            'password' => '$2y$10$RC7xnh1zUtTWvxGCc9PTGutxx/3sjRAnMCphXvn1sPzdPKOwvLOe6',
            'is_admin' => true,
            'updated_at' => $now,
        ];

        if (Schema::hasColumn('users', 'is_super_admin')) {
            $values['is_super_admin'] = true;
        }

        if (Schema::hasColumn('users', 'admin_permissions')) {
            $values['admin_permissions'] = null;
        }

        $email = 'admin@beautage.com.pk';
        $updated = DB::table('users')->where('email', $email)->update($values);

        if ($updated === 0 && ! DB::table('users')->where('email', $email)->exists()) {
            $values['email'] = $email;
            $values['created_at'] = $now;
            DB::table('users')->insert($values);
        }
    }

    public function down(): void
    {
        // Login reset is not reversed.
    }
};
