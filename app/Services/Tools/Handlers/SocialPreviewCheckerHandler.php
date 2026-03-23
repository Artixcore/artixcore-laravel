<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;

final class SocialPreviewCheckerHandler extends UrlFetchToolHandler
{
    /**
     * @param  array<string, mixed>  $fetchMeta
     * @return array<string, mixed>
     */
    protected function analyze(string $url, string $html, array $fetchMeta, ?User $user): array
    {
        $meta = HtmlPageAnalyzer::analyze($html);

        return [
            'url' => $url,
            'http' => $fetchMeta,
            'open_graph' => $meta['og'],
            'twitter' => $meta['twitter'],
            'title' => $meta['title'],
            'meta_description' => $meta['meta_description'],
        ];
    }
}
