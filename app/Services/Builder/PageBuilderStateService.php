<?php

namespace App\Services\Builder;

use App\Models\Page;
use App\Models\PageVersion;
use App\Models\User;
use App\Support\Builder\BuilderDocumentDefaults;
use App\Support\Builder\BuilderDocumentValidator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PageBuilderStateService
{
    public function __construct(
        private BuilderDocumentValidator $validator,
        private BuilderDocumentSanitizer $sanitizer,
    ) {}

    /**
     * @return array{page: Page, document: array<string, mixed>, latest_version_id: int|null}
     */
    public function load(Page $page): array
    {
        $latest = $page->versions()->orderByDesc('id')->first();

        if ($latest === null) {
            $document = BuilderDocumentDefaults::emptyDocument();
            $latest = $this->persistVersion($page, $document, null, 'initial');

            return [
                'page' => $page->fresh(),
                'document' => $document,
                'latest_version_id' => $latest->id,
            ];
        }

        return [
            'page' => $page,
            'document' => is_array($latest->document_json) ? $latest->document_json : BuilderDocumentDefaults::emptyDocument(),
            'latest_version_id' => $latest->id,
        ];
    }

    /**
     * @param  array<string, mixed>  $document
     */
    public function saveDraft(
        Page $page,
        array $document,
        User $user,
        string $label = 'autosave',
        ?int $baseVersionId = null,
    ): PageVersion {
        $document = $this->sanitizer->sanitizeDocument($document);
        $this->validator->validate($document);

        if ($baseVersionId !== null) {
            $currentMax = (int) $page->versions()->max('id');
            if ($currentMax > $baseVersionId) {
                throw new InvalidArgumentException('conflict');
            }
        }

        return $this->persistVersion($page, $document, $user, $label);
    }

    /**
     * @param  array<string, mixed>  $document
     */
    public function restoreVersion(Page $page, PageVersion $version, User $user): PageVersion
    {
        if ($version->page_id !== $page->id) {
            abort(404);
        }

        $document = is_array($version->document_json)
            ? $version->document_json
            : BuilderDocumentDefaults::emptyDocument();

        return $this->persistVersion($page, $document, $user, 'restore:'.$version->id);
    }

    /**
     * @param  array<string, mixed>  $document
     */
    private function persistVersion(Page $page, array $document, ?User $user, string $label): PageVersion
    {
        return PageVersion::query()->create([
            'page_id' => $page->id,
            'user_id' => $user?->id,
            'label' => mb_substr($label, 0, 32),
            'document_json' => $document,
        ]);
    }

    public function publish(Page $page, ?\DateTimeInterface $scheduledAt = null): void
    {
        $latest = $page->versions()->orderByDesc('id')->first();
        if ($latest === null) {
            throw new InvalidArgumentException('No document version to publish.');
        }

        $document = is_array($latest->document_json)
            ? $latest->document_json
            : BuilderDocumentDefaults::emptyDocument();

        $document = $this->sanitizer->sanitizeDocument($document);
        $this->validator->validate($document);

        $compiler = app(PageDocumentCompiler::class);

        DB::transaction(function () use ($page, $document, $compiler, $scheduledAt): void {
            $compiler->compileAndPersist($page, $document);

            if ($scheduledAt !== null) {
                $page->update([
                    'status' => 'draft',
                    'scheduled_publish_at' => $scheduledAt,
                    'published_at' => null,
                    'archived_at' => null,
                ]);
            } else {
                $page->update([
                    'status' => 'published',
                    'published_at' => now(),
                    'scheduled_publish_at' => null,
                    'archived_at' => null,
                ]);
            }
        });
    }

    public function archive(Page $page): void
    {
        $page->update([
            'status' => 'archived',
            'archived_at' => now(),
        ]);
    }

    public function unpublish(Page $page): void
    {
        $page->update([
            'status' => 'draft',
            'published_at' => null,
            'scheduled_publish_at' => null,
        ]);
    }
}
