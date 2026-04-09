<?php

namespace App\Services\Builder;

use App\Models\Page;
use App\Models\PageBlock;
use App\Support\Builder\BuilderNodeType;
use App\Support\Content\PageBlockType;
use Illuminate\Support\Facades\DB;

class PageDocumentCompiler
{
    public function __construct(
        private BuilderEmbedSanitizer $sanitizer,
    ) {}

    /**
     * Replace all page_blocks from builder document (transactional).
     *
     * @param  array<string, mixed>  $document
     */
    public function compileAndPersist(Page $page, array $document): void
    {
        $blocks = $this->compileToBlocks($document);

        DB::transaction(function () use ($page, $blocks): void {
            PageBlock::query()->where('page_id', $page->id)->delete();
            foreach ($blocks as $i => $row) {
                PageBlock::query()->create([
                    'page_id' => $page->id,
                    'sort_order' => $i,
                    'type' => $row['type'],
                    'data' => $row['data'],
                ]);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $document
     * @return list<array{type: string, data: array<string, mixed>}>
     */
    public function compileToBlocks(array $document): array
    {
        $root = $document['root'] ?? null;
        if (! is_array($root)) {
            return [];
        }

        $out = [];
        $children = $root['children'] ?? [];
        if (! is_array($children)) {
            return [];
        }

        foreach ($children as $child) {
            if (is_array($child)) {
                $this->walkNode($child, $out);
            }
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $node
     * @param  list<array{type: string, data: array<string, mixed>}>  $out
     */
    private function walkNode(array $node, array &$out): void
    {
        $type = $node['type'] ?? '';

        if (in_array($type, [
            BuilderNodeType::Root->value,
            BuilderNodeType::Section->value,
            BuilderNodeType::Columns->value,
            BuilderNodeType::Column->value,
        ], true)) {
            $children = $node['children'] ?? [];
            if (is_array($children)) {
                foreach ($children as $child) {
                    if (is_array($child)) {
                        $this->walkNode($child, $out);
                    }
                }
            }

            return;
        }

        if ($type === BuilderNodeType::Hero->value) {
            $out[] = [
                'type' => PageBlockType::Hero->value,
                'data' => $this->mapHero($node),
            ];

            return;
        }

        if ($type === BuilderNodeType::FeatureGrid->value) {
            $out[] = [
                'type' => PageBlockType::FeatureGrid->value,
                'data' => $this->mapFeatureGrid($node),
            ];

            return;
        }

        if ($type === BuilderNodeType::Cta->value) {
            $out[] = [
                'type' => PageBlockType::Cta->value,
                'data' => $this->mapCta($node),
            ];

            return;
        }

        if ($type === BuilderNodeType::RichText->value) {
            $out[] = [
                'type' => PageBlockType::RichText->value,
                'data' => $this->mapRichText($node),
            ];

            return;
        }

        if ($type === BuilderNodeType::Heading->value) {
            $out[] = [
                'type' => PageBlockType::RichText->value,
                'data' => $this->mapHeadingToRichText($node),
            ];

            return;
        }

        if ($type === BuilderNodeType::Paragraph->value) {
            $out[] = [
                'type' => PageBlockType::RichText->value,
                'data' => $this->mapParagraphToRichText($node),
            ];

            return;
        }

        if ($type === BuilderNodeType::Image->value) {
            $out[] = [
                'type' => PageBlockType::RichText->value,
                'data' => $this->mapImageToRichText($node),
            ];

            return;
        }

        if ($type === BuilderNodeType::Button->value) {
            $out[] = [
                'type' => PageBlockType::RichText->value,
                'data' => $this->mapButtonToRichText($node),
            ];

            return;
        }

        if ($type === BuilderNodeType::Spacer->value || $type === BuilderNodeType::Divider->value) {
            $out[] = [
                'type' => PageBlockType::RichText->value,
                'data' => [
                    'html' => $type === BuilderNodeType::Divider->value
                        ? '<hr />'
                        : '<div class="builder-spacer" style="height:1.5rem" aria-hidden="true"></div>',
                ],
            ];

            return;
        }

        if ($type === BuilderNodeType::Embed->value) {
            $props = is_array($node['props'] ?? null) ? $node['props'] : [];
            $safe = $this->sanitizer->sanitizeEmbedProps($props);
            if (($safe['src'] ?? '') !== '') {
                $title = htmlspecialchars((string) ($safe['title'] ?? ''), ENT_QUOTES, 'UTF-8');
                $src = htmlspecialchars((string) $safe['src'], ENT_QUOTES, 'UTF-8');
                $out[] = [
                    'type' => PageBlockType::RichText->value,
                    'data' => [
                        'html' => '<iframe src="'.$src.'" title="'.$title.'" loading="lazy" referrerpolicy="no-referrer" sandbox="allow-scripts allow-same-origin allow-popups allow-forms"></iframe>',
                    ],
                ];
            }

            return;
        }

        // Fallback: stringify unknown blocks as rich text note (keeps API stable)
        $out[] = [
            'type' => PageBlockType::RichText->value,
            'data' => [
                'html' => '<p>'.htmlspecialchars('Unsupported block: '.$type, ENT_QUOTES, 'UTF-8').'</p>',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    private function mapHero(array $node): array
    {
        $p = is_array($node['props'] ?? null) ? $node['props'] : [];

        return [
            'eyebrow' => is_string($p['eyebrow'] ?? null) ? $p['eyebrow'] : '',
            'title' => is_string($p['title'] ?? null) ? $p['title'] : '',
            'subtitle' => is_string($p['subtitle'] ?? null) ? $p['subtitle'] : '',
            'primaryCta' => $this->mapCtaLink($p['primaryCta'] ?? null),
            'secondaryCta' => $this->mapCtaLink($p['secondaryCta'] ?? null),
        ];
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    private function mapFeatureGrid(array $node): array
    {
        $p = is_array($node['props'] ?? null) ? $node['props'] : [];
        $heading = is_string($p['heading'] ?? null) ? $p['heading'] : '';
        $items = $p['items'] ?? [];
        $clean = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $clean[] = [
                    'title' => is_string($item['title'] ?? null) ? $item['title'] : '',
                    'description' => is_string($item['description'] ?? null) ? $item['description'] : '',
                    'href' => is_string($item['href'] ?? null) ? $item['href'] : '',
                ];
            }
        }

        return ['heading' => $heading, 'items' => $clean];
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    private function mapCta(array $node): array
    {
        $p = is_array($node['props'] ?? null) ? $node['props'] : [];

        return [
            'title' => is_string($p['title'] ?? null) ? $p['title'] : '',
            'body' => is_string($p['body'] ?? null) ? $p['body'] : '',
            'buttonLabel' => is_string($p['buttonLabel'] ?? null) ? $p['buttonLabel'] : 'Learn more',
            'href' => is_string($p['href'] ?? null) ? $p['href'] : '/contact',
        ];
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    private function mapRichText(array $node): array
    {
        $p = is_array($node['props'] ?? null) ? $node['props'] : [];
        $html = is_string($p['html'] ?? null) ? $p['html'] : '';

        return ['html' => $this->sanitizer->sanitizeHtml($html)];
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    private function mapHeadingToRichText(array $node): array
    {
        $p = is_array($node['props'] ?? null) ? $node['props'] : [];
        $text = is_string($p['text'] ?? null) ? $p['text'] : '';
        $level = (int) ($p['level'] ?? 2);
        $level = max(1, min(4, $level));
        $tag = 'h'.$level;
        $safe = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        return ['html' => "<{$tag}>{$safe}</{$tag}>"];
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    private function mapParagraphToRichText(array $node): array
    {
        $p = is_array($node['props'] ?? null) ? $node['props'] : [];
        $text = is_string($p['text'] ?? null) ? $p['text'] : '';
        $safe = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        return ['html' => '<p>'.$safe.'</p>'];
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    private function mapImageToRichText(array $node): array
    {
        $p = is_array($node['props'] ?? null) ? $node['props'] : [];
        $src = is_string($p['src'] ?? null) ? $p['src'] : '';
        $alt = is_string($p['alt'] ?? null) ? $p['alt'] : '';
        if ($src === '' || ! str_starts_with(strtolower($src), 'http')) {
            return ['html' => ''];
        }
        $srcEsc = htmlspecialchars($src, ENT_QUOTES, 'UTF-8');
        $altEsc = htmlspecialchars($alt, ENT_QUOTES, 'UTF-8');

        return ['html' => '<p><img src="'.$srcEsc.'" alt="'.$altEsc.'" loading="lazy" /></p>'];
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    private function mapButtonToRichText(array $node): array
    {
        $p = is_array($node['props'] ?? null) ? $node['props'] : [];
        $label = is_string($p['label'] ?? null) ? $p['label'] : 'Button';
        $href = is_string($p['href'] ?? null) ? $p['href'] : '#';
        $labelEsc = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
        $hrefEsc = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');

        return ['html' => '<p><a href="'.$hrefEsc.'">'.$labelEsc.'</a></p>'];
    }

    /**
     * @param  mixed  $raw
     * @return array{label: string, href: string}
     */
    private function mapCtaLink(mixed $raw): array
    {
        if (! is_array($raw)) {
            return ['label' => '', 'href' => ''];
        }

        return [
            'label' => is_string($raw['label'] ?? null) ? $raw['label'] : '',
            'href' => is_string($raw['href'] ?? null) ? $raw['href'] : '',
        ];
    }
}
