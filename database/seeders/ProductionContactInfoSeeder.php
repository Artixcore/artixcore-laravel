<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * Idempotent: updates site_settings.contact_email when empty or set to known placeholders.
 * Safe to run on production. Does not delete rows or touch CMS JSON blobs.
 *
 *   php artisan db:seed --class=ProductionContactInfoSeeder --force
 */
class ProductionContactInfoSeeder extends Seeder
{
    public function run(): void
    {
        $target = (string) config('app.contact_email', 'hello@artixcore.com');

        $legacy = [
            'hello@artixcore.test',
            'hello@example.com',
            'info@example.com',
            'contact@example.com',
        ];

        $settings = SiteSetting::instance();
        $current = trim((string) ($settings->contact_email ?? ''));

        if ($current === '' || in_array(strtolower($current), $legacy, true)) {
            $settings->contact_email = $target;
            $settings->save();
        }
    }
}
