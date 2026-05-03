<?php

namespace App\Services;

/**
 * Minimal HTML allowlist for trusted CMS/admin rich text (defense in depth).
 */
class HtmlSanitizer
{
    private const ALLOWED_TAGS = '<p><br><strong><em><ul><ol><li><a><h2><h3><h4><blockquote><code><pre><img>';

    public function sanitize(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $stripped = preg_replace('#<(script|style)[^>]*>.*?</\\1>#is', '', $html) ?? $html;
        $stripped = preg_replace('/href\s*=\s*"\s*javascript:[^"]*"/i', 'href="#"', $stripped) ?? $stripped;
        $stripped = preg_replace('/href\s*=\s*\'\s*javascript:[^\']*\'/i', "href='#'", $stripped) ?? $stripped;

        $stripped = strip_tags($stripped, self::ALLOWED_TAGS);

        return $this->sanitizeImgTags($stripped);
    }

    /**
     * Allow only http(s) image sources; strip other attributes except alt/title/loading/width/height.
     */
    private function sanitizeImgTags(string $html): string
    {
        return preg_replace_callback('/<img\b[^>]*>/i', function (array $m): string {
            $tag = $m[0];
            if (! preg_match('/src\s*=\s*"(?<q1>[^"]*)"|src\s*=\s*\'(?<q2>[^\']*)\'/i', $tag, $sm)) {
                return '';
            }
            $src = $sm['q1'] ?? $sm['q2'] ?? '';
            $src = trim(html_entity_decode($src, ENT_QUOTES | ENT_HTML5));
            if ($src === '' || ! preg_match('#^https?://#i', $src)) {
                return '';
            }

            $alt = '';
            if (preg_match('/alt\s*=\s*"([^"]*)"/i', $tag, $am)) {
                $alt = htmlspecialchars($am[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            } elseif (preg_match("/alt\s*=\s*'([^']*)'/i", $tag, $am)) {
                $alt = htmlspecialchars($am[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            $attrs = 'src="'.htmlspecialchars($src, ENT_QUOTES | ENT_HTML5, 'UTF-8').'" alt="'.$alt.'" loading="lazy"';

            return '<img '.$attrs.'>';
        }, $html) ?? $html;
    }

    /**
     * Sanitize for public display (same rules; call explicitly for clarity).
     */
    public function sanitizeForPublic(?string $html): string
    {
        return $this->sanitize($html);
    }

    /**
     * Add rel to external links in allowed anchor tags (best-effort).
     */
    public function hardenLinks(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        return preg_replace_callback('/<a\s+([^>]*)>/i', function (array $m): string {
            $inner = $m[1];
            if (! preg_match('/href\s*=\s*"(?<h>[^"]*)"|href\s*=\s*\'(?<h2>[^\']*)\'/i', $inner, $hm)) {
                return '<a '.$inner.'>';
            }
            $href = html_entity_decode($hm['h'] ?? $hm['h2'] ?? '', ENT_QUOTES | ENT_HTML5);
            if ($href !== '' && preg_match('#^https?://#i', $href) && ! str_starts_with($href, config('app.url'))) {
                if (stripos($inner, 'rel=') !== false) {
                    return '<a '.$inner.'>';
                }

                return '<a '.$inner.' rel="noopener noreferrer nofollow">';
            }

            return '<a '.$inner.'>';
        }, $html) ?? $html;
    }
}
