<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Support\HtmlPageAnalyzer;

final class WebsiteTechnologyHandler extends UrlFetchToolHandler
{
    /**
     * @param  array<string, mixed>  $fetchMeta
     * @return array<string, mixed>
     */
    protected function analyze(string $url, string $html, array $fetchMeta, ?User $user): array
    {
        return [
            'url' => $url,
            'http' => $fetchMeta,
            'technology_hints' => HtmlPageAnalyzer::technologyHints($html),
            'disclaimer' => 'Heuristic signals from a single HTML response only; verify independently.',
        ];
    }
}
