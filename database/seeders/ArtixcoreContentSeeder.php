<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Orchestrates production-safe marketing/content seeds (all updateOrCreate).
 */
class ArtixcoreContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ServiceSeeder::class,
            SaasPlatformSeeder::class,
            FaqSeeder::class,
            TestimonialSeeder::class,
            PortfolioSeeder::class,
            ContentRelationSeeder::class,
        ]);
    }
}
