<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\AjaxFormEnvelope;
use App\Http\Support\AjaxRequestExpectations;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageAdminController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', ContactMessage::class);

        return view('admin.contact-messages.index', [
            'messages' => ContactMessage::query()->orderByDesc('created_at')->paginate(30),
        ]);
    }

    public function show(ContactMessage $contactMessage): View
    {
        $this->authorize('view', $contactMessage);
        $contactMessage->markRead();

        return view('admin.contact-messages.show', ['message' => $contactMessage]);
    }

    public function markRead(ContactMessage $contactMessage): JsonResponse|RedirectResponse
    {
        $this->authorize('view', $contactMessage);
        $contactMessage->markRead();

        return AjaxFormEnvelope::success(__('Marked as read.'));
    }

    public function destroy(Request $request, ContactMessage $contactMessage): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $contactMessage);
        $contactMessage->delete();

        if (AjaxRequestExpectations::prefersJsonResponse($request)) {
            return AjaxFormEnvelope::success(__('Message deleted.'));
        }

        return redirect()->route('admin.contact-messages.index')->with('status', 'Message deleted.');
    }
}
