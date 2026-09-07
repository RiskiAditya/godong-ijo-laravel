<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(private ChatbotService $chatbot)
    {
    }

    public function publicReply(Request $request): JsonResponse
    {
        return $this->respond($request, 'public');
    }

    public function adminReply(Request $request): JsonResponse
    {
        return $this->respond($request, 'admin');
    }

    private function respond(Request $request, string $audience): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        return response()->json($this->chatbot->reply($validated['message'], $audience));
    }
}
