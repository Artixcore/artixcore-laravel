<?php

namespace Database\Seeders;

use App\Models\SeoSetting;
use App\Services\SeoSettingsService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Idempotent post-deploy orchestrator for public homepage/CMS defaults.
 * Does not delete data or overwrite intentional production content beyond sibling seeders' rules.
 *
 *   php artisan db:seed --class=ProductionHomepageRepairSeeder --force
 */
class ProductionHomepageRepairSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ProductionContactInfoSeeder::class);
        $this->call(ProductionContentRepairSeeder::class);
        $this->seedBaselineSeoRowsIfEmpty();
    }

    /**
     * Ensures seo_settings has rows so admin/API snapshot queries behave consistently.
     * Only runs when the table exists and is completely empty (does not replace existing SEO config).
     */
    private function seedBaselineSeoRowsIfEmpty(): void
    {
        if (! Schema::hasTable('seo_settings')) {
            return;
        }

        try {
            if (SeoSetting::query()->exists()) {
                return;
            }
        } catch (\Throwable) {
            return;
        }

        $payload = $this->baselineDisabledSeoPayload();
        app(SeoSettingsService::class)->syncFromValidated($payload);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function baselineDisabledSeoPayload(): array
    {
        $seo = [];
        $fieldActive = SeoSettingsService::fieldActiveKeys();

        foreach (SeoSettingsService::keySchema() as $platform => $keys) {
            $block = ['enabled' => false];
            foreach ($keys as $key) {
                if ($key === 'enabled') {
                    continue;
                }
                $block[$key] = '';
                if (in_array($key, $fieldActive[$platform] ?? [], true)) {
                    $block[$key.'_active'] = true;
                }
            }
            $seo[$platform] = $block;
        }

        return $seo;
    }
}
