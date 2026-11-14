<?php

namespace App\Console\Commands;

use Database\Seeders\AdminUserSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmptyStoreCatalog extends Command
{
    protected $signature = 'store:empty-catalog';

    protected $description = 'Clear products, orders, and customers. Keep the admin login and site settings.';

    /** @var list<string> */
    private array $keep = [
        'migrations',
        'site_settings',
        'users',
        'cms_pages',
        'chatbot_rules',
        'password_reset_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
    ];

    public function handle(): int
    {
        $cleared = [];

        Schema::disableForeignKeyConstraints();

        foreach (Schema::getTableListing() as $table) {
            $name = $this->tableName($table);
            if (in_array($name, $this->keep, true) || ! Schema::hasTable($name)) {
                continue;
            }

            DB::table($name)->truncate();
            $cleared[] = $name;
        }

        DB::table('users')->where('is_admin', false)->delete();
        DB::table('sessions')->truncate();
        DB::table('password_reset_tokens')->truncate();

        Schema::enableForeignKeyConstraints();

        $this->call('db:seed', [
            '--class' => AdminUserSeeder::class,
            '--force' => true,
        ]);

        $admin = DB::table('users')->where('is_admin', true)->orderBy('id')->first();

        $this->info('Cleared '.count($cleared).' tables.');
        if ($admin) {
            $this->line('Admin kept: '.$admin->email);
        }

        return self::SUCCESS;
    }

    private function tableName(string $table): string
    {
        $parts = explode('.', $table);

        return (string) end($parts);
    }
}
