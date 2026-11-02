<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateProductAiRequest;
use App\Services\ProductAi\ProductAiException;
use App\Services\ProductAi\ProductAiService;
use Illuminate\Http\JsonResponse;

class ProductAiController extends Controller
{
    public function __invoke(GenerateProductAiRequest $request, ProductAiService $ai): JsonResponse
    {
        if (! $ai->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'AI is not configured. Add OPENAI_API_KEY and/or GEMINI_API_KEY to the server .env file.',
            ], 422);
        }

        $data = $request->validated();

        try {
            $content = $ai->generate(
                (string) $data['name'],
                $data['target_keyword'] ?? null,
                $data['specs'] ?? null,
                $data['category'] ?? null,
                $data['provider'] ?? ProductAiService::PROVIDER_AUTO,
            );

            return response()->json([
                'success' => true,
                'data' => $content->toArray(),
                'form' => $content->toFormState(),
            ]);
        } catch (ProductAiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Could not generate SEO content. Please try again.',
            ], 500);
        }
    }
}
