<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CaseStudyMarketTaxonomySeeder extends Seeder
{
    /**
     * Idempotent categories/tags used by case studies and market-update editorial workflows.
     */
    public function run(): void
    {
        $categoriesTax = Taxonomy::query()->firstOrCreate(
            ['slug' => 'categories'],
            ['name' => 'Categories']
        );

        $tagsTax = Taxonomy::query()->firstOrCreate(
            ['slug' => 'tags'],
            ['name' => 'Tags']
        );

        $categoryTree = [
            'Case Studies' => [
                'Enterprise SaaS',
                'Platform modernization',
                'R&D collaboration',
                'Startup launch',
                'Infrastructure & reliability',
            ],
            'Market Updates' => [
                'Quantum computing',
                'Physics & advanced science',
                'Semiconductors',
                'Climate tech',
                'Space & aerospace',
                'AI infrastructure',
            ],
        ];

        foreach ($categoryTree as $parentName => $children) {
            $parent = Term::query()->updateOrCreate(
                ['taxonomy_id' => $categoriesTax->id, 'slug' => Str::slug($parentName)],
                ['name' => $parentName, 'sort_order' => 0, 'parent_id' => null]
            );

            $order = 0;
            foreach ($children as $childName) {
                Term::query()->updateOrCreate(
                    [
                        'taxonomy_id' => $categoriesTax->id,
                        'parent_id' => $parent->id,
                        'slug' => Str::slug($childName),
                    ],
                    ['name' => $childName, 'sort_order' => $order++]
                );
            }
        }

        $extraTags = [
            'Concept study',
            'Verified delivery',
            'Market outlook',
            'Risk-aware analysis',
            'Technical deep dive',
            'Industry scan',
        ];

        foreach ($extraTags as $i => $name) {
            Term::query()->updateOrCreate(
                ['taxonomy_id' => $tagsTax->id, 'slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $i + 100, 'parent_id' => null]
            );
        }
    }
}
