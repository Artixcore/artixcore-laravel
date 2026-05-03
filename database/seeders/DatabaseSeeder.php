<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            RolePermissionSeeder::class,
            MasterAdminSeeder::class,
            UserSeeder::class,
            ContentSeeder::class,
            BuilderTemplateSeeder::class,
            MicroToolsNavSeeder::class,
            MicroToolCategoriesSeeder::class,
            MicroToolsSeeder::class,
            MarketingBladeSeeder::class,
            CaseStudyMarketTaxonomySeeder::class,
            ArticleCategoryTagSeeder::class,
            SeoSettingsSeeder::class,
            ArtixcoreContentSeeder::class,
        ]);
    }
}
