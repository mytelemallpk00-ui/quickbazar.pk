<?php

namespace App\Http\Controllers;

use App\Support\WebmasterTools;
use Symfony\Component\HttpFoundation\Response;

class IndexNowKeyController
{
    public function __invoke(string $key): Response
    {
        $expected = WebmasterTools::indexNowKey();
        if ($expected === null || ! hash_equals($expected, strtolower($key))) {
            abort(404);
        }

        return response($expected, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
