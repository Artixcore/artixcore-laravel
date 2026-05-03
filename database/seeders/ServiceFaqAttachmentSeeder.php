<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceFaqAttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $saasFaqs = [
            [
                'seed_key' => 'faq.saas.full-platform',
                'question' => 'Can Artixcore build a full SaaS platform?',
                'answer' => 'Yes—multi-tenant models, onboarding, admin tooling, billing integrations, and observability are common delivery themes.',
                'category' => 'SaaS Development',
                'sort_order' => 20,
            ],
            [
                'seed_key' => 'faq.saas.billing',
                'question' => 'Do you support subscription billing?',
                'answer' => 'Yes—Stripe and similar providers, invoicing workflows, dunning, and partner revenue splits can be integrated where required.',
                'category' => 'SaaS Development',
                'sort_order' => 21,
            ],
            [
                'seed_key' => 'faq.saas.dashboards',
                'question' => 'Can you build admin dashboards and user portals?',
                'answer' => 'Yes—role-based access, audit trails, and operational dashboards are standard patterns we implement with Laravel and modern frontends.',
                'category' => 'SaaS Development',
                'sort_order' => 22,
            ],
            [
                'seed_key' => 'faq.saas.ai',
                'question' => 'Can you integrate AI into SaaS workflows?',
                'answer' => 'Yes—typically behind authenticated APIs with policy gates, rate limits, and human-in-the-loop review where appropriate.',
                'category' => 'SaaS Development',
                'sort_order' => 23,
            ],
            [
                'seed_key' => 'faq.saas.cloud',
                'question' => 'Can you deploy on DigitalOcean, AWS, or other cloud providers?',
                'answer' => 'Yes—Artixcore ships production-ready deployments with environment parity, backups, and scaling guidance.',
                'category' => 'SaaS Development',
                'sort_order' => 24,
            ],
        ];

        foreach ($saasFaqs as $row) {
            $key = $row['seed_key'];
            unset($row['seed_key']);
            Faq::query()->updateOrCreate(
                ['seed_key' => $key],
                array_merge($row, [
                    'seed_key' => $key,
                    'is_published' => true,
                    'status' => 'published',
                    'show_on_general_faq' => true,
                    'show_on_saas_page' => false,
                ])
            );
        }

        $this->attach('app-development', [
            'faq.app.timeline',
            'faq.app.platforms',
            'faq.app.backend',
            'faq.app.ai',
            'faq.app.maintenance',
        ]);

        $this->attach('saas-development', [
            'faq.saas.full-platform',
            'faq.saas.billing',
            'faq.saas.dashboards',
            'faq.saas.ai',
            'faq.saas.cloud',
        ]);
    }

    /**
     * @param  list<string>  $seedKeys
     */
    private function attach(string $serviceSlug, array $seedKeys): void
    {
        $service = Service::query()->where('slug', $serviceSlug)->first();
        if (! $service) {
            return;
        }

        foreach ($seedKeys as $i => $key) {
            $faq = Faq::query()->where('seed_key', $key)->first();
            if (! $faq) {
                continue;
            }
            if (! $service->faqs()->where('faqs.id', $faq->id)->exists()) {
                $service->faqs()->attach($faq->id, ['sort_order' => $i * 10]);
            }
        }
    }
}
