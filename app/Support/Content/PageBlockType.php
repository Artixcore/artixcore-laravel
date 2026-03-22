<?php

namespace App\Support\Content;

/**
 * Canonical page block `type` values (must stay in sync with the Next.js block renderer).
 */
enum PageBlockType: string
{
    case Hero = 'hero';
    case FeatureGrid = 'feature_grid';
    case ProductShowcase = 'product_showcase';
    case ResearchHighlight = 'research_highlight';
    case ArticleRail = 'article_rail';
    case Cta = 'cta';
    case RichText = 'rich_text';

    /**
     * @return array<string, string>
     */
    public static function filamentOptions(): array
    {
        $map = [
            self::Hero->value => 'Hero',
            self::FeatureGrid->value => 'Feature grid',
            self::ProductShowcase->value => 'Product showcase',
            self::ResearchHighlight->value => 'Research highlight',
            self::ArticleRail->value => 'Article rail',
            self::Cta->value => 'CTA',
            self::RichText->value => 'Rich text',
        ];

        return $map;
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function toApiList(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[] = [
                'value' => $case->value,
                'label' => self::filamentOptions()[$case->value] ?? $case->value,
            ];
        }

        return $out;
    }
}
