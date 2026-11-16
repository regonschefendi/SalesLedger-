<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function publicKey(WebPushService $webPush): JsonResponse
    {
        return response()->json([
            'publicKey' => $webPush->publicKey(),
        ]);
    }

    public function store(Request $request, WebPushService $webPush): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => 'required|string|max:512',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
            'contentEncoding' => 'nullable|string|in:aesgcm,aes128gcm',
        ]);

        /** @var User $user */
        $user = $request->user();

        $webPush->subscribe($user, $data);

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request, WebPushService $webPush): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => 'required|string|max:512',
        ]);

        /** @var User $user */
        $user = $request->user();

        $webPush->unsubscribe($user, $data['endpoint']);

        return response()->json(['success' => true]);
    }
}
