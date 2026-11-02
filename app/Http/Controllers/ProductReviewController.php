<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductReviewRequest;
use App\Models\Product;
use App\Models\ProductReview;
use App\Support\SiteSettings;
use Illuminate\Http\JsonResponse;

class ProductReviewController extends Controller
{
    public function store(StoreProductReviewRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();
        $autoPublish = SiteSettings::reviewsAutoApprove();

        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                if ($file->isValid()) {
                    $photoPaths[] = $file->store('reviews/'.$product->id, 'public');
                }
            }
        }

        ProductReview::query()->create([
            'product_id' => $product->id,
            'author_name' => $data['author_name'],
            'author_email' => $data['author_email'] ?? null,
            'rating' => (int) $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'],
            'photos' => $photoPaths ?: null,
            'is_verified_buyer' => false,
            'is_published' => $autoPublish,
        ]);

        $message = $autoPublish
            ? 'Thank you! Your review and photos are now live on the product page.'
            : 'Thank you! Your review was submitted and will appear after our team approves it.';

        return response()->json([
            'message' => $message,
            'published' => $autoPublish,
        ]);
    }
}
