<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmReviewController extends Controller
{
    use RespondsWithAdminJson;

    public function __construct(
        private ActivityLogger $activityLogger,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Testimonial::class);

        $q = Testimonial::query()->with(['avatarMedia', 'companyLogoMedia']);

        if ($request->filled('status')) {
            $q->where('moderation_status', $request->string('status')->toString());
        }

        return view('admin.crm.reviews.index', [
            'reviews' => $q->orderByDesc('id')->paginate(25)->withQueryString(),
            'filters' => $request->only(['status']),
        ]);
    }

    public function approve(Request $request, Testimonial $testimonial): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $testimonial);
        if (! $request->user()?->can('reviews.publish')) {
            abort(403);
        }

        $testimonial->update([
            'moderation_status' => 'approved',
            'is_published' => true,
            'published_at' => $testimonial->published_at ?? now(),
            'updated_by' => $request->user()?->id,
        ]);
        $this->activityLogger->log('crm.review.approved', $testimonial, ['id' => $testimonial->id], $request);

        return $this->adminRespond($request, 'Review approved.', route('admin.crm.reviews.index'));
    }

    public function reject(Request $request, Testimonial $testimonial): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $testimonial);
        if (! $request->user()?->can('reviews.publish')) {
            abort(403);
        }

        $testimonial->update([
            'moderation_status' => 'rejected',
            'is_published' => false,
            'updated_by' => $request->user()?->id,
        ]);
        $this->activityLogger->log('crm.review.rejected', $testimonial, ['id' => $testimonial->id], $request);

        return $this->adminRespond($request, 'Review rejected.', route('admin.crm.reviews.index'));
    }

}
