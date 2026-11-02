<?php

namespace App\Http\Controllers;

use App\Support\BundleDealsCatalog;
use Illuminate\View\View;

class ExclusiveOffersController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.exclusive-offers', [
            'deals' => BundleDealsCatalog::activeDeals(),
        ]);
    }
}
