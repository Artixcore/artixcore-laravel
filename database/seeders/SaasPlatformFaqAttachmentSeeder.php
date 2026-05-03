<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Product;
use Illuminate\Database\Seeder;

class SaasPlatformFaqAttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $dealzyro = [
            [
                'seed_key' => 'faq.dealzyro.what',
                'question' => 'What is Dealzyro?',
                'answer' => 'Dealzyro is an Artixcore SaaS platform for POS, inventory, multi-channel commerce, partner programs, subscriptions, and secure payments.',
                'sort_order' => 10,
            ],
            [
                'seed_key' => 'faq.dealzyro.pos',
                'question' => 'Can Dealzyro support POS and inventory?',
                'answer' => 'Yes—store operations, catalog, stock movements, and channel sync are first-class workflows.',
                'sort_order' => 11,
            ],
            [
                'seed_key' => 'faq.dealzyro.white-label',
                'question' => 'Does Dealzyro support white-label partners?',
                'answer' => 'Yes—partner onboarding, branding, and revenue attribution can be configured for white-label programs.',
                'sort_order' => 12,
            ],
            [
                'seed_key' => 'faq.dealzyro.import',
                'question' => 'Can retailers import products in bulk?',
                'answer' => 'Yes—bulk import pipelines and validation are supported with AI-assisted catalog onboarding where configured.',
                'sort_order' => 13,
            ],
            [
                'seed_key' => 'faq.dealzyro.payments',
                'question' => 'What payment methods can be integrated?',
                'answer' => 'Common providers and custom gateways can be integrated depending on compliance and market requirements.',
                'sort_order' => 14,
            ],
        ];

        $prosperofy = [
            [
                'seed_key' => 'faq.prosperofy.what',
                'question' => 'What is Prosperofy?',
                'answer' => 'Prosperofy is engineered backend-first: a Laravel core exposes standardized APIs for modular SaaS delivery.',
                'sort_order' => 10,
            ],
            [
                'seed_key' => 'faq.prosperofy.laravel',
                'question' => 'Why does Prosperofy use Laravel core as the API gateway?',
                'answer' => 'Laravel provides mature auth, queues, policies, and ecosystem packages that reduce bespoke security drift.',
                'sort_order' => 11,
            ],
            [
                'seed_key' => 'faq.prosperofy.endpoints',
                'question' => 'Should frontends use existing `/api/auth` and `/api/app` endpoints?',
                'answer' => 'Yes—those endpoints are the supported integration contract to keep clients consistent and auditable.',
                'sort_order' => 12,
            ],
            [
                'seed_key' => 'faq.prosperofy.multi-app',
                'question' => 'Can Prosperofy support multiple frontend apps?',
                'answer' => 'Yes—multiple clients can consume the same core APIs with tenant-aware policies and tokens.',
                'sort_order' => 13,
            ],
            [
                'seed_key' => 'faq.prosperofy.growth',
                'question' => 'Is Prosperofy built for secure modular growth?',
                'answer' => 'Yes—modules, boundaries, and observability are designed in from day one to support iterative expansion.',
                'sort_order' => 14,
            ],
        ];

        foreach ($dealzyro as $row) {
            $this->upsertFaq($row);
        }
        foreach ($prosperofy as $row) {
            $this->upsertFaq($row);
        }

        $this->attachProduct('dealzyro', array_column($dealzyro, 'seed_key'));
        $this->attachProduct('prosperofy', array_column($prosperofy, 'seed_key'));
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function upsertFaq(array $row): void
    {
        $key = $row['seed_key'];
        unset($row['seed_key']);
        Faq::query()->updateOrCreate(
            ['seed_key' => $key],
            array_merge($row, [
                'seed_key' => $key,
                'category' => $row['category'] ?? 'SaaS Platform',
                'is_published' => true,
                'status' => 'published',
                'show_on_general_faq' => false,
                'show_on_saas_page' => false,
            ])
        );
    }

    /**
     * @param  list<string>  $seedKeys
     */
    private function attachProduct(string $slug, array $seedKeys): void
    {
        $product = Product::query()->where('slug', $slug)->first();
        if (! $product) {
            return;
        }

        foreach ($seedKeys as $i => $key) {
            $faq = Faq::query()->where('seed_key', $key)->first();
            if (! $faq) {
                continue;
            }
            if (! $product->faqs()->where('faqs.id', $faq->id)->exists()) {
                $product->faqs()->attach($faq->id, ['sort_order' => $i * 10]);
            }
        }
    }
}
