<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Support\HtmlPageAnalyzer;

final class WebsiteAuditBasicHandler extends UrlFetchToolHandler
{
    /**
     * @param  array<string, mixed>  $fetchMeta
     * @return array<string, mixed>
     */
    protected function analyze(string $url, string $html, array $fetchMeta, ?User $user): array
    {
        $meta = HtmlPageAnalyzer::analyze($html);

        $issues = [];
        if ($meta['title'] === null || $meta['title'] === '') {
            $issues[] = 'missing_title';
        }
        if ($meta['meta_description'] === null || $meta['meta_description'] === '') {
            $issues[] = 'missing_meta_description';
        }
        if ($meta['canonical'] === null || $meta['canonical'] === '') {
            $issues[] = 'missing_canonical';
        }
        if ($meta['viewport'] === null || $meta['viewport'] === '') {
            $issues[] = 'missing_viewport_meta';
        }

        return [
            'url' => $url,
            'http' => $fetchMeta,
            'summary' => [
                'title' => $meta['title'],
                'meta_description_length' => $meta['meta_description'] !== null ? mb_strlen($meta['meta_description']) : 0,
                'h1_count' => count($meta['h1_sample']),
                'h1_sample' => $meta['h1_sample'],
            ],
            'issues' => $issues,
        ];
    }
}
