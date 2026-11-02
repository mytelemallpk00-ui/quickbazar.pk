<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\GoogleMerchant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GoogleMerchantFeedController extends Controller
{
    public function __invoke(Request $request): Response
    {
        if (! GoogleMerchant::isFeedEnabled()) {
            abort(404);
        }

        $expected = GoogleMerchant::feedAccessToken();
        if ($expected !== null && ! hash_equals($expected, (string) $request->query('token', ''))) {
            abort(403, 'Invalid feed token.');
        }

        $products = Product::query()
            ->with('productCategory')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $xml = view('feeds.google-merchant', [
            'storeUrl' => GoogleMerchant::storefrontUrl(),
            'storeName' => GoogleMerchant::brandName(),
            'brand' => GoogleMerchant::brandName(),
            'currency' => GoogleMerchant::currencyCode(),
            'country' => GoogleMerchant::targetCountry(),
            'products' => $products,
        ])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=900',
        ]);
    }
}
