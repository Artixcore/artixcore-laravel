<?php

namespace Database\Seeders;

use App\Models\CrmEmailTemplate;
use Illuminate\Database\Seeder;

class CrmEmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Initial reply',
                'subject' => 'Thanks for reaching out to Artixcore',
                'body' => "Hi,\n\nThanks for contacting Artixcore. We've received your note and will follow up shortly with next steps.\n\nBest,\nArtixcore",
            ],
            [
                'name' => 'Project discovery follow-up',
                'subject' => 'Discovery follow-up — Artixcore',
                'body' => "Hi,\n\nFollowing up on our discovery conversation. Could you share any timelines, integrations, or constraints we should factor into the proposal?\n\nThanks,\nArtixcore",
            ],
            [
                'name' => 'Proposal follow-up',
                'subject' => 'Proposal follow-up',
                'body' => "Hi,\n\nChecking whether you had a chance to review the proposal. Happy to walk through questions on scope, milestones, or hosting.\n\nBest,\nArtixcore",
            ],
            [
                'name' => 'Thank you',
                'subject' => 'Thank you',
                'body' => "Hi,\n\nThank you for the time today. We'll summarize decisions and share the agreed next steps shortly.\n\nBest,\nArtixcore",
            ],
            [
                'name' => 'Meeting request',
                'subject' => 'Meeting request — Artixcore',
                'body' => "Hi,\n\nWould you be available for a short call this week to align on goals and constraints? Suggest a few times that work for you and we'll confirm.\n\nThanks,\nArtixcore",
            ],
        ];

        foreach ($templates as $tpl) {
            CrmEmailTemplate::query()->updateOrCreate(
                ['name' => $tpl['name']],
                [
                    'subject' => $tpl['subject'],
                    'body' => $tpl['body'],
                    'is_active' => true,
                ]
            );
        }
    }
}
