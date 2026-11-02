<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class NewsletterSubscribeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'list' => ['required', 'in:store,partner'],
        ]);

        if (! Schema::hasTable('newsletter_subscribers')) {
            return response()->json([
                'message' => 'Thank you for subscribing. We will be in touch soon.',
            ]);
        }

        NewsletterSubscriber::query()->updateOrCreate(
            [
                'email' => strtolower($validated['email']),
                'list' => $validated['list'],
            ],
            [
                'source' => $request->header('Referer'),
            ],
        );

        $message = $validated['list'] === NewsletterSubscriber::LIST_PARTNER
            ? 'You are subscribed to Beautage partner updates.'
            : 'Thank you for subscribing! Check your inbox for offers and new arrivals.';

        return response()->json(['message' => $message]);
    }
}
