<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleCategoryTagSeeder extends Seeder
{
    public function run(): void
    {
        $categoriesTax = Taxonomy::query()->updateOrCreate(
            ['slug' => 'categories'],
            ['name' => 'Categories']
        );

        $tagsTax = Taxonomy::query()->updateOrCreate(
            ['slug' => 'tags'],
            ['name' => 'Tags']
        );

        $tree = [
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
        ];

        foreach ($tree as $parentName => $children) {
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

        $tagNames = [
            'Laravel', 'React', 'AI', 'SaaS', 'Cybersecurity', 'Cloud', 'DevOps',
            'Automation', 'Product', 'Engineering', 'Startups', 'Enterprise',
        ];

        foreach ($tagNames as $i => $name) {
            Term::query()->updateOrCreate(
                ['taxonomy_id' => $tagsTax->id, 'slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $i, 'parent_id' => null]
            );
        }
    }
}
