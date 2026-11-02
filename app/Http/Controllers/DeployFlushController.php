<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

class DeployFlushController extends Controller
{
    public function __invoke(): Response
    {
        $path = storage_path('app/deploy-flush.token');
        if (! is_file($path)) {
            abort(404);
        }

        $expected = trim((string) file_get_contents($path));
        $given = (string) request()->query('token');

        if ($expected === '' || ! hash_equals($expected, $given)) {
            abort(403);
        }

        @unlink($path);

        Artisan::call('view:clear');
        Artisan::call('config:clear');

        if (is_file(storage_path('app/deploy-run-migrations.flag'))) {
            @unlink(storage_path('app/deploy-run-migrations.flag'));
            Artisan::call('migrate', ['--force' => true]);
        }

        try {
            Artisan::call('optimize:clear');
        } catch (\Throwable) {
            // Hostinger shared hosting may still serve; view/config clear is enough for Blade deploys.
        }

        return response('OK', 200);
    }
}
