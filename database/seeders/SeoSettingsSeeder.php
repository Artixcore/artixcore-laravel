<?php

namespace Database\Seeders;

use App\Models\SeoSetting;
use Illuminate\Database\Seeder;

class SeoSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $rows = [];

        $defaults = [
            'meta' => [
                'enabled' => '0',
                'pixel_id' => '',
                'app_id' => '',
                'og_title_override' => '',
                'og_description_override' => '',
                'og_image_url' => '',
            ],
            'google' => [
                'enabled' => '0',
                'ga4_measurement_id' => '',
                'gtm_container_id' => '',
                'adsense_publisher_id' => '',
                'search_console_verification' => '',
            ],
            'twitter' => [
                'enabled' => '0',
                'card_type' => 'summary_large_image',
                'site_handle' => '',
                'creator_handle' => '',
            ],
            'tiktok' => [
                'enabled' => '0',
                'pixel_id' => '',
                'event_settings' => '',
            ],
            'additional' => [
                'enabled' => '0',
                'linkedin_partner_id' => '',
                'pinterest_verification' => '',
            ],
        ];

        foreach ($defaults as $platform => $keys) {
            foreach ($keys as $key => $value) {
                $rows[] = [
                    'platform' => $platform,
                    'key' => $key,
                    'value' => $value,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach ($rows as $row) {
            SeoSetting::query()->updateOrInsert(
                ['platform' => $row['platform'], 'key' => $row['key']],
                [
                    'value' => $row['value'],
                    'is_active' => $row['is_active'],
                    'updated_at' => $row['updated_at'],
                    'created_at' => $row['created_at'],
                ]
            );
        }
    }
}
