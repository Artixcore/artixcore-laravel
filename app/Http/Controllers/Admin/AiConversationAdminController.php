<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use Illuminate\View\View;

class AiConversationAdminController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', AiConversation::class);

        return view('admin.ai-conversations.index', [
            'conversations' => AiConversation::query()
                ->with(['agent', 'lead'])
                ->orderByDesc('last_message_at')
                ->orderByDesc('id')
                ->paginate(30),
        ]);
    }

    public function show(AiConversation $ai_conversation): View
    {
        $this->authorize('view', $ai_conversation);

        $ai_conversation->load(['agent', 'lead', 'messages']);

        return view('admin.ai-conversations.show', [
            'conversation' => $ai_conversation,
        ]);
    }
}
