<?php

namespace App\Services\Builder;

class BuilderEmbedSanitizer
{
    /**
     * Allow only safe tags for rich text / embed fragments.
     *
     * @param  list<string>  $allowedTags
     */
    public function sanitizeHtml(string $html, array $allowedTags = [
        'p', 'br', 'strong', 'em', 'u', 'a', 'ul', 'ol', 'li',
        'h1', 'h2', 'h3', 'h4', 'blockquote', 'code', 'pre', 'span',
        'img',
    ]): string {
        $strip = array_map(static fn (string $t): string => "<{$t}>", $allowedTags);

        return strip_tags($html, implode('', $strip));
    }

    /**
     * Embed node: allow iframe with https only src (basic guard).
     *
     * @param  array<string, mixed>  $props
     * @return array<string, mixed>
     */
    public function sanitizeEmbedProps(array $props): array
    {
        $src = $props['src'] ?? '';
        if (! is_string($src) || $src === '') {
            return ['src' => '', 'title' => '', 'sandbox' => true];
        }

        if (! str_starts_with(strtolower($src), 'https://')) {
            return ['src' => '', 'title' => '', 'sandbox' => true];
        }

        $title = $props['title'] ?? '';
        if (! is_string($title)) {
            $title = '';
        }

        return [
            'src' => $src,
            'title' => mb_substr($title, 0, 200),
            'sandbox' => true,
        ];
    }
}
