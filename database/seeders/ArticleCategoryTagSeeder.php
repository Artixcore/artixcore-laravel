<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeds article categories and tags (delegates to focused seeders).
 */
class ArticleCategoryTagSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ArticleCategorySeeder::class,
            ArticleTagSeeder::class,
        ]);
    }
}
