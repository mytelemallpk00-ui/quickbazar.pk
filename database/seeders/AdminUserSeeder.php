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
                'name' => 'Beautage Admin',
                'password' => Hash::make('Beautage@Admin2026'),
                'is_admin' => true,
            ],
        );
    }
}
