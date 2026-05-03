<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Idempotent article tags under `tags` taxonomy.
 *
 * php artisan db:seed --class=ArticleTagSeeder --force
 */
class ArticleTagSeeder extends Seeder
{
    public function run(): void
    {
        $tagsTax = Taxonomy::query()->updateOrCreate(
            ['slug' => 'tags'],
            ['name' => 'Tags']
        );

        $tagNames = [
            'AI',
            'SaaS',
            'Laravel',
            'React',
            'Cloud',
            'Cybersecurity',
            'Automation',
            'Startups',
            'Digital Transformation',
            'Quantum Computing',
            'Physics',
            'Market Trends',
            'Software Engineering',
        ];

        foreach ($tagNames as $i => $name) {
            Term::query()->updateOrCreate(
                ['taxonomy_id' => $tagsTax->id, 'slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $i, 'parent_id' => null]
            );
        }
    }
}
