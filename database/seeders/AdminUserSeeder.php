<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@beautage.com.pk'],
            [
                'name' => 'Shop Pakistan Admin',
                'password' => Hash::make('Qb#Admin2026'),
                'is_admin' => true,
                'is_super_admin' => true,
            ],
        );
    }
}
