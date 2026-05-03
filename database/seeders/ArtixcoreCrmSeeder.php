<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Idempotent CRM bootstrap for production: sources, email templates, FAQ seeds and pivots.
 */
class ArtixcoreCrmSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CrmSourceSeeder::class,
            CrmEmailTemplateSeeder::class,
            FaqSeeder::class,
            ServiceFaqAttachmentSeeder::class,
            SaasPlatformFaqAttachmentSeeder::class,
        ]);
    }
}
