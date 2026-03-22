<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request): JsonResponse
    {
        $data = $request->validated();

        Log::info('contact.submission', [
            'name' => $data['name'],
            'email' => $data['email'],
            'company' => $data['company'] ?? null,
            'message' => $data['message'],
        ]);

        return response()->json([
            'message' => 'Thanks — we received your message and will reply soon.',
        ]);
    }
}
