<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Support\HtmlPageAnalyzer;

final class SchemaMarkupCheckerHandler extends UrlFetchToolHandler
{
    /**
     * @param  array<string, mixed>  $fetchMeta
     * @return array<string, mixed>
     */
    protected function analyze(string $url, string $html, array $fetchMeta, ?User $user): array
    {
        $blocks = HtmlPageAnalyzer::jsonLdBlocks($html);

        return [
            'url' => $url,
            'http' => $fetchMeta,
            'json_ld_blocks' => count($blocks),
            'json_ld' => $blocks,
        ];
    }
}
