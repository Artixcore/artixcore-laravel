<?php

namespace Database\Seeders;

use App\Models\MicroToolCategory;
use Illuminate\Database\Seeder;

class MicroToolCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Web tools', 'slug' => 'web', 'sort_order' => 10],
            ['name' => 'Domain & DNS', 'slug' => 'domain-dns', 'sort_order' => 20],
            ['name' => 'Security & trust', 'slug' => 'security-trust', 'sort_order' => 30],
            ['name' => 'Media', 'slug' => 'media', 'sort_order' => 40],
            ['name' => 'SEO & content', 'slug' => 'seo-content', 'sort_order' => 50],
            ['name' => 'Developer', 'slug' => 'developer', 'sort_order' => 60],
            ['name' => 'Marketing', 'slug' => 'marketing', 'sort_order' => 70],
        ];

        foreach ($rows as $row) {
            MicroToolCategory::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'sort_order' => $row['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
