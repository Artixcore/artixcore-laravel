<?php

namespace App\Services\Tools\Handlers;

use App\Models\User;
use App\Services\Tools\Contracts\ToolHandlerInterface;
use InvalidArgumentException;

class KeywordDensityHandler implements ToolHandlerInterface
{
    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function handle(array $input, ?User $user): array
    {
        $text = isset($input['text']) ? trim((string) $input['text']) : '';
        if ($text === '') {
            throw new InvalidArgumentException('text is required.');
        }
        if (mb_strlen($text) > 200_000) {
            throw new InvalidArgumentException('text is too long (max 200k characters).');
        }

        $lower = mb_strtolower($text);
        $words = preg_split('/[^\p{L}\p{N}]+/u', $lower, -1, PREG_SPLIT_NO_EMPTY);
        if (! is_array($words) || $words === []) {
            return ['word_count' => 0, 'top_terms' => []];
        }

        $counts = [];
        foreach ($words as $w) {
            if (mb_strlen($w) < 2) {
                continue;
            }
            $counts[$w] = ($counts[$w] ?? 0) + 1;
        }

        arsort($counts);
        $top = [];
        $i = 0;
        $total = count($words);
        foreach ($counts as $term => $c) {
            if ($i++ >= 25) {
                break;
            }
            $top[] = [
                'term' => $term,
                'count' => $c,
                'density_percent' => round($total > 0 ? ($c / $total) * 100 : 0, 3),
            ];
        }

        return [
            'word_count' => $total,
            'unique_terms' => count($counts),
            'top_terms' => $top,
        ];
    }
}
