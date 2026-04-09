<?php

namespace App\Services\Ai\Builder;

use App\Models\AiBuilderBusinessProfile;
use App\Models\AiGenerationLog;
use App\Models\Page;
use App\Services\Ai\Exceptions\LlmTransportException;
use App\Services\Ai\LlmRouter;
use App\Services\Builder\BuilderTreeManipulator;
use Illuminate\Support\Facades\Log;
use JsonException;
use Throwable;

class AiBuilderOrchestrator
{
    public function __construct(
        private LlmRouter $router,
        private BuilderTreeManipulator $tree,
    ) {}

    /**
     * @param  array<string, mixed>  $document  Current builder document
     * @return array{rationale: string, document: array<string, mixed>, provider_id: ?int, prompt_tokens: ?int, completion_tokens: ?int}
     */
    public function propose(
        Page $page,
        array $document,
        string $userPrompt,
        ?string $targetNodeId,
        ?int $userId,
    ): array {
        $profile = AiBuilderBusinessProfile::instance();
        $profileJson = json_encode($this->profileToArray($profile), JSON_THROW_ON_ERROR);
        $docJson = json_encode($document, JSON_THROW_ON_ERROR);

        $system = <<<'PROMPT'
You are an expert landing-page strategist and conversion copywriter. You output ONLY valid JSON (no markdown fences).
The JSON must have this shape:
{
  "rationale": "short explanation of your choices",
  "mode": "full_document" | "replace_node" | "append_child",
  "document": { ... }  // required when mode is full_document: full schema v1 document with schemaVersion 1 and root node
  "target_id": "uuid",  // required when mode is replace_node or append_child
  "node": { ... }      // replacement subtree when mode is replace_node; or fragment to append when mode is append_child
}

Document rules:
- schemaVersion must be 1.
- root node type must be "root", with a unique string "id" (UUID), "version": 1, "props": {}, "children": [...].
- Allowed child types include: section, columns, column, hero, heading, paragraph, image, button, feature_grid, cta, rich_text, spacer, divider, embed, testimonial_grid, pricing_table, faq, contact_form, newsletter, gallery, map, social_links.
- section, columns, column may nest children. Leaf blocks use props for content.
- hero props: eyebrow, title, subtitle, primaryCta {label,href}, secondaryCta {label,href}.
- feature_grid props: heading, items[{title,description,href}].
- cta props: title, body, buttonLabel, href.
- rich_text props: html (simple semantic HTML).
- heading props: text, level (1-4). paragraph props: text. image props: src (https), alt. button props: label, href.
- Respect forbidden topics and claims from business profile; never invent certifications or guarantees not implied by the profile.

When mode is full_document, include a complete polished page structure (hero, features, CTA, FAQ optional).
When mode is replace_node, return only the replacement subtree in "node" with the SAME id as target_id.
When mode is append_child, parent is target_id (usually a section or column id); "node" is the new subtree to append.
PROMPT;

        $user = "Business profile JSON:\n{$profileJson}\n\nPage title: {$page->title}\nPage path: {$page->path}\n\nCurrent document JSON:\n{$docJson}\n\nUser request:\n{$userPrompt}\n";
        if ($targetNodeId !== null && $targetNodeId !== '') {
            $user .= "\nFocused target node id: {$targetNodeId}\n";
        }

        $messages = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $user],
        ];

        try {
            $result = $this->router->complete($messages, null);
        } catch (Throwable $e) {
            $this->logFailure($userId, $page, $userPrompt, null, $e);

            throw $e;
        }

        try {
            $parsed = $this->parseJsonFromModel($result->content);
        } catch (JsonException $e) {
            Log::warning('ai.builder_bad_json', ['error' => $e->getMessage()]);
            $this->logFailure($userId, $page, $userPrompt, $result->aiProviderId, $e, 'bad_json');

            throw new LlmTransportException('AI returned invalid JSON.');
        }

        $rationale = is_string($parsed['rationale'] ?? null) ? $parsed['rationale'] : '';
        $mode = is_string($parsed['mode'] ?? null) ? $parsed['mode'] : 'full_document';

        $outDoc = match ($mode) {
            'replace_node' => $this->applyReplace($document, $parsed),
            'append_child' => $this->applyAppend($document, $parsed),
            default => $this->applyFull($parsed),
        };

        AiGenerationLog::query()->create([
            'user_id' => $userId,
            'page_id' => $page->id,
            'page_version_id' => null,
            'ai_provider_id' => $result->aiProviderId,
            'action' => 'propose',
            'request_summary' => mb_substr($userPrompt, 0, 512),
            'prompt_tokens' => $result->promptTokens,
            'completion_tokens' => $result->completionTokens,
            'status' => 'ok',
            'metadata_json' => ['mode' => $mode],
        ]);

        return [
            'rationale' => $rationale,
            'document' => $outDoc,
            'provider_id' => $result->aiProviderId,
            'prompt_tokens' => $result->promptTokens,
            'completion_tokens' => $result->completionTokens,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function profileToArray(AiBuilderBusinessProfile $p): array
    {
        return [
            'business_name' => $p->business_name,
            'brand_summary' => $p->brand_summary,
            'business_type' => $p->business_type,
            'target_audience' => $p->target_audience,
            'main_services' => $p->main_services,
            'unique_selling_points' => $p->unique_selling_points,
            'tone_of_voice' => $p->tone_of_voice,
            'offer_details' => $p->offer_details,
            'location' => $p->location,
            'contact_details' => $p->contact_details,
            'preferred_cta_goal' => $p->preferred_cta_goal,
            'writing_style' => $p->writing_style,
            'forbidden_topics' => $p->forbidden_topics,
            'brand_colors' => $p->brand_colors,
            'style_notes' => $p->style_notes,
        ];
    }

    /**
     * @param  array<string, mixed>  $parsed
     * @return array<string, mixed>
     */
    private function applyFull(array $parsed): array
    {
        $doc = $parsed['document'] ?? null;
        if (! is_array($doc)) {
            throw new LlmTransportException('AI response missing document.');
        }

        return $doc;
    }

    /**
     * @param  array<string, mixed>  $document
     * @param  array<string, mixed>  $parsed
     * @return array<string, mixed>
     */
    private function applyReplace(array $document, array $parsed): array
    {
        $targetId = $parsed['target_id'] ?? '';
        $node = $parsed['node'] ?? null;
        if (! is_string($targetId) || $targetId === '' || ! is_array($node)) {
            throw new LlmTransportException('AI replace_node response incomplete.');
        }

        return $this->tree->replaceNodeById($document, $targetId, $node);
    }

    /**
     * @param  array<string, mixed>  $document
     * @param  array<string, mixed>  $parsed
     * @return array<string, mixed>
     */
    private function applyAppend(array $document, array $parsed): array
    {
        $parentId = $parsed['target_id'] ?? '';
        $node = $parsed['node'] ?? null;
        if (! is_string($parentId) || $parentId === '' || ! is_array($node)) {
            throw new LlmTransportException('AI append_child response incomplete.');
        }

        return $this->tree->appendChild($document, $parentId, $node);
    }

    /**
     * @return array<string, mixed>
     */
    private function parseJsonFromModel(string $content): array
    {
        $t = trim($content);
        if (preg_match('/^```(?:json)?\s*([\s\S]*?)\s*```$/u', $t, $m)) {
            $t = trim($m[1]);
        }

        $decoded = json_decode($t, true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($decoded)) {
            throw new JsonException('Not an object');
        }

        return $decoded;
    }

    private function logFailure(
        ?int $userId,
        Page $page,
        string $userPrompt,
        ?int $providerId,
        Throwable $e,
        string $status = 'error',
    ): void {
        AiGenerationLog::query()->create([
            'user_id' => $userId,
            'page_id' => $page->id,
            'page_version_id' => null,
            'ai_provider_id' => $providerId,
            'action' => 'propose',
            'request_summary' => mb_substr($userPrompt, 0, 512),
            'prompt_tokens' => null,
            'completion_tokens' => null,
            'status' => $status,
            'metadata_json' => ['error' => $e->getMessage()],
        ]);
    }
}
