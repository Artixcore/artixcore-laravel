<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreWebContactRequest;
use App\Http\Support\AjaxRequestExpectations;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    use RespondsWithJson;

    public function create(): View
    {
        return view('pages.contact');
    }

    public function store(StoreWebContactRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $message = ContactMessage::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'company' => $data['company'] ?? null,
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'ip_address' => $request->ip(),
        ]);

        if (AjaxRequestExpectations::prefersJsonResponse($request)) {
            return $this->successResponse(
                __('Thanks — we received your message and will reply soon.'),
                ['id' => $message->id],
            );
        }

        return redirect()->route('contact')->with('status', 'Thanks — we received your message and will reply soon.');
    }
}
