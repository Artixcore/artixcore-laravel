<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Support\HtmlPageAnalyzer;

final class MobileFriendlyHintsHandler extends UrlFetchToolHandler
{
    /**
     * @param  array<string, mixed>  $fetchMeta
     * @return array<string, mixed>
     */
    protected function analyze(string $url, string $html, array $fetchMeta, ?User $user): array
    {
        $meta = HtmlPageAnalyzer::analyze($html);
        $hints = [];
        if ($meta['viewport'] !== null && $meta['viewport'] !== '') {
            $hints[] = 'viewport_meta_present';
        } else {
            $hints[] = 'viewport_meta_missing';
        }
        if (preg_match('/<meta[^>]+name=["\']apple-mobile-web-app-capable["\']/i', $html)) {
            $hints[] = 'apple_web_app_meta';
        }

        return [
            'url' => $url,
            'http' => $fetchMeta,
            'viewport' => $meta['viewport'],
            'title' => $meta['title'],
            'hints' => $hints,
        ];
    }
}
