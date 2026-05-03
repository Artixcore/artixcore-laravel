<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CrmContactNoteRequest;
use App\Http\Requests\Admin\CrmContactRequest;
use App\Http\Requests\Admin\CrmSendEmailRequest;
use App\Mail\CrmContactEmail;
use App\Models\CrmContact;
use App\Models\CrmContactNote;
use App\Models\CrmEmailTemplate;
use App\Models\Service;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CrmContactController extends Controller
{
    use RespondsWithAdminJson;

    public function __construct(
        private ActivityLogger $activityLogger,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', CrmContact::class);

        $allowedSort = ['created_at', 'name', 'email', 'status', 'priority', 'company_name'];
        $sort = in_array($request->string('sort')->toString(), $allowedSort, true)
            ? $request->string('sort')->toString()
            : 'created_at';
        $dir = $request->string('dir')->toString() === 'asc' ? 'asc' : 'desc';

        $q = CrmContact::query()->with(['source', 'assignee', 'service']);

        if ($request->filled('status')) {
            $q->where('status', $request->string('status')->toString());
        }
        if ($request->filled('source_id')) {
            $q->where('source_id', $request->integer('source_id'));
        }
        if ($request->filled('service_id')) {
            $q->where('service_id', $request->integer('service_id'));
        }
        if ($request->filled('industry')) {
            $q->where('industry', $request->string('industry')->toString());
        }
        if ($request->filled('priority')) {
            $q->where('priority', $request->string('priority')->toString());
        }
        if ($request->filled('assigned_to')) {
            $q->where('assigned_to', $request->integer('assigned_to'));
        }
        if ($request->filled('q')) {
            $needle = '%'.$request->string('q')->toString().'%';
            $q->where(function ($w) use ($needle): void {
                $w->where('name', 'like', $needle)
                    ->orWhere('email', 'like', $needle)
                    ->orWhere('phone', 'like', $needle)
                    ->orWhere('company_name', 'like', $needle);
            });
        }

        $contacts = $q->orderBy($sort, $dir)->paginate(20)->withQueryString();

        return view('admin.crm.contacts.index', [
            'contacts' => $contacts,
            'sources' => \App\Models\CrmSource::query()->orderBy('sort_order')->orderBy('name')->get(),
            'services' => Service::query()->orderBy('title')->get(['id', 'title']),
            'admins' => User::query()->orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['status', 'source_id', 'service_id', 'industry', 'priority', 'assigned_to', 'q', 'sort', 'dir']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CrmContact::class);

        return view('admin.crm.contacts.form', $this->formPayload(new CrmContact, 'create'));
    }

    public function store(CrmContactRequest $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', CrmContact::class);
        $data = $request->validated();
        $data['created_by'] = $request->user()?->id;
        $data['updated_by'] = $request->user()?->id;

        $contact = CrmContact::query()->create($data);
        $this->activityLogger->log('crm.contact.created', $contact, ['id' => $contact->id], $request);

        return $this->adminRespond(
            $request,
            'Contact created.',
            route('admin.crm.contacts.show', $contact),
            ['contact_id' => $contact->id]
        );
    }

    public function show(CrmContact $crmContact): View
    {
        $this->authorize('view', $crmContact);

        $crmContact->load(['source', 'assignee', 'service', 'saasPlatform', 'project', 'lead', 'notes.user']);

        $templates = CrmEmailTemplate::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.crm.contacts.show', [
            'contact' => $crmContact,
            'emailTemplates' => $templates,
        ]);
    }

    public function edit(CrmContact $crmContact): View
    {
        $this->authorize('update', $crmContact);

        return view('admin.crm.contacts.form', $this->formPayload($crmContact, 'edit'));
    }

    public function update(CrmContactRequest $request, CrmContact $crmContact): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $crmContact);
        $data = $request->validated();
        $data['updated_by'] = $request->user()?->id;
        $crmContact->update($data);
        $this->activityLogger->log('crm.contact.updated', $crmContact, ['id' => $crmContact->id], $request);

        return $this->adminRespond($request, 'Contact updated.', route('admin.crm.contacts.show', $crmContact));
    }

    public function destroy(Request $request, CrmContact $crmContact): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $crmContact);
        $this->activityLogger->log('crm.contact.deleted', $crmContact, ['id' => $crmContact->id], $request);
        $crmContact->delete();

        return $this->adminRespond($request, 'Contact deleted.', route('admin.crm.contacts.index'));
    }

    public function storeNote(CrmContactNoteRequest $request, CrmContact $crmContact): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $crmContact);

        CrmContactNote::query()->create([
            'contact_id' => $crmContact->id,
            'user_id' => $request->user()?->id,
            'type' => $request->validated('type'),
            'title' => $request->validated('title'),
            'body' => $request->validated('body'),
            'metadata' => null,
        ]);

        $this->activityLogger->log('crm.contact.note_added', $crmContact, [], $request);

        return $this->adminRespond($request, 'Note added.', route('admin.crm.contacts.show', $crmContact));
    }

    public function updateStatus(Request $request, CrmContact $crmContact): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $crmContact);

        $request->validate([
            'status' => ['required', 'string', Rule::in(CrmContact::STATUSES)],
        ]);

        $crmContact->update([
            'status' => $request->string('status')->toString(),
            'updated_by' => $request->user()?->id,
        ]);

        CrmContactNote::query()->create([
            'contact_id' => $crmContact->id,
            'user_id' => $request->user()?->id,
            'type' => CrmContactNote::TYPE_STATUS_CHANGE,
            'title' => 'Status updated',
            'body' => 'Status changed to '.$request->string('status')->toString().'.',
            'metadata' => ['status' => $request->string('status')->toString()],
        ]);

        $this->activityLogger->log('crm.contact.status_changed', $crmContact, ['status' => $request->string('status')->toString()], $request);

        return $this->adminRespond($request, 'Status updated.', route('admin.crm.contacts.show', $crmContact));
    }

    public function sendEmail(CrmSendEmailRequest $request, CrmContact $crmContact): JsonResponse|RedirectResponse
    {
        $this->authorize('email', $crmContact);

        if (! config('crm.email_enabled')) {
            return $this->adminRespond(
                $request,
                'CRM outbound email is disabled (CRM_EMAIL_ENABLED).',
                null,
                [],
                422
            );
        }

        $email = $crmContact->email;
        if ($email === null || $email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->adminRespond($request, 'Contact has no valid email address.', null, [], 422);
        }

        $subject = $request->validated('subject');
        $body = $request->validated('body');
        $templateId = $request->validated('template_id');

        try {
            Mail::to($email)->send(new CrmContactEmail($subject, $body));
        } catch (Throwable $e) {
            report($e);
            Log::warning('CRM email send failed.', ['contact_id' => $crmContact->id, 'exception' => $e::class]);

            return $this->adminRespond(
                $request,
                'Email could not be sent. Check mail configuration.',
                null,
                [],
                422
            );
        }

        CrmContactNote::query()->create([
            'contact_id' => $crmContact->id,
            'user_id' => $request->user()?->id,
            'type' => CrmContactNote::TYPE_EMAIL,
            'title' => $subject,
            'body' => __('Email sent to :email.', ['email' => $email]),
            'metadata' => [
                'subject' => $subject,
                'template_id' => $templateId,
            ],
        ]);

        $this->activityLogger->log('crm.contact.email_sent', $crmContact, [
            'subject' => $subject,
            'template_id' => $templateId,
        ], $request);

        return $this->adminRespond($request, 'Email sent.', route('admin.crm.contacts.show', $crmContact));
    }

    /**
     * @return array<string, mixed>
     */
    private function formPayload(CrmContact $contact, string $mode): array
    {
        return [
            'contact' => $contact,
            'mode' => $mode,
            'sources' => \App\Models\CrmSource::query()->active()->orderBy('sort_order')->orderBy('name')->get(),
            'services' => Service::query()->orderBy('title')->get(),
            'products' => \App\Models\Product::query()->orderBy('title')->get(['id', 'title']),
            'projects' => \App\Models\CrmProject::query()->orderByDesc('id')->limit(200)->get(),
            'admins' => User::query()->orderBy('name')->get(['id', 'name']),
        ];
    }
}
