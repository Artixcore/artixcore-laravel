<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;

final class SpeedSnapshotHandler extends UrlFetchToolHandler
{
    /**
     * @param  array<string, mixed>  $fetchMeta
     * @return array<string, mixed>
     */
    protected function analyze(string $url, string $html, array $fetchMeta, ?User $user): array
    {
        $assetHints = [
            'script_tags_approx' => preg_match_all('/<script\b/i', $html) ?: 0,
            'stylesheet_links_approx' => preg_match_all('/<link[^>]+rel=["\']stylesheet["\']/i', $html) ?: 0,
            'img_tags_approx' => preg_match_all('/<img\b/i', $html) ?: 0,
        ];

        return [
            'url' => $url,
            'final_url' => $fetchMeta['final_url'],
            'status' => $fetchMeta['status'],
            'timing_ms' => $fetchMeta['timing_ms'],
            'html_bytes' => $fetchMeta['content_length_bytes'],
            'asset_hints' => $assetHints,
        ];
    }
}
