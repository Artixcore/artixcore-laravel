<?php

namespace App\Services;

/**
 * Minimal HTML allowlist for trusted CMS/admin rich text (defense in depth).
 */
class HtmlSanitizer
{
    private const ALLOWED_TAGS = '<p><br><strong><em><ul><ol><li><a><h2><h3><blockquote>';

    public function sanitize(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $stripped = preg_replace('#<(script|style)[^>]*>.*?</\\1>#is', '', $html) ?? $html;
        $stripped = preg_replace('/href\s*=\s*"\s*javascript:[^"]*"/i', 'href="#"', $stripped) ?? $stripped;
        $stripped = preg_replace('/href\s*=\s*\'\s*javascript:[^\']*\'/i', "href='#'", $stripped) ?? $stripped;

        return strip_tags($stripped, self::ALLOWED_TAGS);
    }
}
