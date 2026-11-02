<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\StoreAutomation\AbandonedCartSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbandonedCartSyncController extends Controller
{
    public function __invoke(Request $request, AbandonedCartSyncService $sync): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.image' => ['nullable', 'string', 'max:500'],
        ]);

        $sync->sync(
            $validated['email'],
            $validated['items'],
            Auth::id(),
        );

        return response()->json(['ok' => true]);
    }
}
