<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GoogleIndexPublishRequest;
use App\Models\Product;
use App\Services\GoogleIndex\ClaudeSeoPreflightService;
use App\Services\GoogleIndex\GoogleIndexException;
use App\Services\GoogleIndex\GoogleIndexService;
use App\Services\GoogleIndex\GoogleIndexUrl;
use Illuminate\Http\JsonResponse;

class GoogleIndexController extends Controller
{
    public function publish(GoogleIndexPublishRequest $request, GoogleIndexService $indexing): JsonResponse
    {
        $data = $request->validated();
        $action = $data['action'] ?? 'URL_UPDATED';

        try {
            $indexable = null;
            $url = $data['url'] ?? null;

            if (! empty($data['product_id'])) {
                $product = Product::query()->findOrFail((int) $data['product_id']);
                $indexable = $product;
                $url = GoogleIndexUrl::forProduct($product);
            }

            $result = $indexing->publish((string) $url, $action, $indexable, $request->user());

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'log_id' => $result['log']->id,
                'status' => $result['log']->status,
            ]);
        } catch (GoogleIndexException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function analyzeProduct(Product $product, ClaudeSeoPreflightService $claude): JsonResponse
    {
        try {
            $analysis = $claude->analyze([
                'url' => GoogleIndexUrl::forProduct($product),
                'title' => $product->meta_title ?: $product->name,
                'description' => $product->meta_description ?: $product->short_description ?: strip_tags((string) $product->description),
                'focus_keyword' => $product->seo_focus_keyword,
            ]);

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
            ]);
        } catch (GoogleIndexException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
