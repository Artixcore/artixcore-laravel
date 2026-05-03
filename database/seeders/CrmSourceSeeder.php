<?php

namespace Database\Seeders;

use App\Models\CrmSource;
use Illuminate\Database\Seeder;

class CrmSourceSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Website Lead Form', 'slug' => CrmSource::SLUG_WEBSITE_LEAD_FORM, 'sort_order' => 5],
            ['name' => 'Contact Form', 'slug' => 'contact-form', 'sort_order' => 10],
            ['name' => 'Referral', 'slug' => 'referral', 'sort_order' => 15],
            ['name' => 'Facebook', 'slug' => 'facebook', 'sort_order' => 20],
            ['name' => 'LinkedIn', 'slug' => 'linkedin', 'sort_order' => 25],
            ['name' => 'Google Search', 'slug' => 'google-search', 'sort_order' => 30],
            ['name' => 'Google Ads', 'slug' => 'google-ads', 'sort_order' => 35],
            ['name' => 'Email Campaign', 'slug' => 'email-campaign', 'sort_order' => 40],
            ['name' => 'WhatsApp', 'slug' => 'whatsapp', 'sort_order' => 45],
            ['name' => 'Phone Call', 'slug' => 'phone-call', 'sort_order' => 50],
            ['name' => 'Upwork', 'slug' => 'upwork', 'sort_order' => 55],
            ['name' => 'Fiverr', 'slug' => 'fiverr', 'sort_order' => 60],
            ['name' => 'Existing Client', 'slug' => 'existing-client', 'sort_order' => 65],
            ['name' => 'Event / Conference', 'slug' => 'event-conference', 'sort_order' => 70],
            ['name' => 'Manual Entry', 'slug' => 'manual-entry', 'sort_order' => 75],
            ['name' => 'Other', 'slug' => 'other', 'sort_order' => 99],
        ];

        foreach ($rows as $i => $row) {
            CrmSource::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'description' => null,
                    'is_active' => true,
                    'sort_order' => $row['sort_order'],
                ]
            );
        }
    }
}
