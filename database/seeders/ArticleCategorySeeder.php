<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Idempotent article/category taxonomy terms under `categories`.
 *
 * php artisan db:seed --class=ArticleCategorySeeder --force
 */
class ArticleCategorySeeder extends Seeder
{
    /**
     * Parent category name => child term names (optional).
     * Slugs must be unique per taxonomy (global), including terms created by CaseStudyMarketTaxonomySeeder.
     *
     * @var array<string, list<string>>
     */
    private function categoryTree(): array
    {
        return [
            'Artificial Intelligence' => ['Generative AI', 'AI Agents', 'AI Automation', 'Machine Learning'],
            'SaaS' => ['Product Strategy', 'SaaS Architecture', 'Growth', 'Billing & Subscriptions'],
            'Software Engineering' => ['Laravel', 'React', 'APIs', 'System Design'],
            'Cybersecurity' => ['Application Security', 'Cloud Security', 'Data Protection'],
            'Cloud & DevOps' => [],
            'Web Development' => [],
            'App Development' => [],
            'Digital Business' => [],
            'Tech Trends' => [],
            'Research & Discovery' => [],
            'Market Updates' => [],
            'Quantum Computing' => [],
            'Physics & Advanced Science' => [],
        ];
    }

    /**
     * Display labels that would collide with existing category slugs (e.g. case study children).
     *
     * @var array<string, string>
     */
    private function parentSlugOverrides(): array
    {
        return [
            'Quantum Computing' => 'quantum-computing-insights',
            'Physics & Advanced Science' => 'physics-advanced-science-insights',
        ];
    }

    public function run(): void
    {
        $categoriesTax = Taxonomy::query()->updateOrCreate(
            ['slug' => 'categories'],
            ['name' => 'Categories']
        );

        $overrides = $this->parentSlugOverrides();

        foreach ($this->categoryTree() as $parentName => $children) {
            $parentSlug = $overrides[$parentName] ?? Str::slug($parentName);
            $parent = Term::query()->updateOrCreate(
                ['taxonomy_id' => $categoriesTax->id, 'slug' => $parentSlug],
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
    }
}
