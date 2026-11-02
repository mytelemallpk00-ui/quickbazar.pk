<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Services\AiSeo\AiSeoManager;
use App\Services\ProductAi\ProductAiException;
use App\Support\AdminPermissions;
use App\Support\AiSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiSeoController extends Controller
{
    public function optimize(Product $product, AiSeoManager $manager, Request $request): JsonResponse
    {
        $this->ensureCatalogAccess();

        $provider = $request->input('provider');

        try {
            $payload = $manager->optimizeProduct($product, is_string($provider) ? $provider : null);

            return response()->json([
                'success' => true,
                'message' => 'Product SEO optimized.',
                'product_id' => $product->getKey(),
                'data' => [
                    'seo_title' => $payload->seoTitle,
                    'meta_description' => $payload->metaDescription,
                    'slug' => $product->slug,
                ],
            ]);
        } catch (ProductAiException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'Optimization failed.'], 500);
        }
    }

    public function optimizeBulk(AiSeoManager $manager, Request $request): JsonResponse
    {
        $this->ensureCatalogAccess();

        @set_time_limit(600);

        $provider = $request->input('provider');
        $onlyUnoptimized = (bool) $request->boolean('only_unoptimized', true);
        $ids = $request->input('product_ids');
        $categoryId = $request->input('category_id');
        $batchSize = max(1, min(25, (int) config('services.ai_seo.bulk_batch_size', 10)));

        if (is_array($ids) && $ids === []) {
            return response()->json([
                'success' => false,
                'message' => 'Select at least one product, or use category bulk optimize.',
            ], 422);
        }

        $query = Product::query()->orderBy('id');

        if (is_numeric($categoryId) && (int) $categoryId > 0) {
            $query->where('category_id', (int) $categoryId);
        }

        if (is_array($ids) && $ids !== []) {
            $query->whereIn('id', array_map('intval', $ids));
        } elseif ($onlyUnoptimized) {
            $query->where(function ($q): void {
                $q->where('is_seo_optimized', false)->orWhereNull('is_seo_optimized');
            });
        }

        $products = $query->limit($batchSize)->get();

        $ok = 0;
        $failed = 0;
        $errors = [];

        foreach ($products as $product) {
            try {
                $manager->optimizeProduct($product, is_string($provider) ? $provider : null);
                $ok++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = ['product_id' => $product->getKey(), 'message' => $e->getMessage()];
                report($e);
            }
        }

        return response()->json([
            'success' => true,
            'processed' => $ok,
            'failed' => $failed,
            'errors' => array_slice($errors, 0, 10),
            'stats' => $manager->stats(),
        ]);
    }

    public function updateProvider(Request $request): JsonResponse
    {
        $this->ensureCatalogAccess();

        $provider = (string) $request->input('provider', 'smart');
        AiSettings::setSeoProvider($provider);

        return response()->json([
            'success' => true,
            'provider' => AiSettings::seoProvider(),
        ]);
    }

    protected function ensureCatalogAccess(): void
    {
        $user = auth()->user();
        if (! $user instanceof User || ! $user->canAccessAdminModule(AdminPermissions::CATALOG)) {
            abort(403);
        }
    }
}
