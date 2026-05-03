<?php

namespace Database\Seeders;

use App\Models\BuilderTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BuilderTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $id = static fn (): string => (string) Str::uuid();

        $saas = [
            'schemaVersion' => 1,
            'root' => [
                'id' => $id(),
                'type' => 'root',
                'version' => 1,
                'props' => [],
                'children' => [
                    [
                        'id' => $id(),
                        'type' => 'section',
                        'version' => 1,
                        'props' => ['paddingY' => 'lg'],
                        'children' => [
                            [
                                'id' => $id(),
                                'type' => 'hero',
                                'version' => 1,
                                'props' => [
                                    'eyebrow' => 'SaaS',
                                    'title' => 'Ship faster with a platform your team trusts',
                                    'subtitle' => 'Automation, analytics, and enterprise-grade security in one place.',
                                    'primaryCta' => ['label' => 'Start free trial', 'href' => '/lead'],
                                    'secondaryCta' => ['label' => 'Book demo', 'href' => '/lead'],
                                ],
                                'children' => [],
                            ],
                        ],
                    ],
                    [
                        'id' => $id(),
                        'type' => 'section',
                        'version' => 1,
                        'props' => [],
                        'children' => [
                            [
                                'id' => $id(),
                                'type' => 'feature_grid',
                                'version' => 1,
                                'props' => [
                                    'heading' => 'Everything you need',
                                    'items' => [
                                        ['title' => 'Workflows', 'description' => 'Automate handoffs across teams.', 'href' => '#'],
                                        ['title' => 'Insights', 'description' => 'Dashboards that leaders actually use.', 'href' => '#'],
                                        ['title' => 'Security', 'description' => 'SOC2-ready controls and audit trails.', 'href' => '#'],
                                    ],
                                ],
                                'children' => [],
                            ],
                        ],
                    ],
                    [
                        'id' => $id(),
                        'type' => 'section',
                        'version' => 1,
                        'props' => [],
                        'children' => [
                            [
                                'id' => $id(),
                                'type' => 'cta',
                                'version' => 1,
                                'props' => [
                                    'title' => 'Ready to scale?',
                                    'body' => 'Join teams who replaced brittle tools with one calm system.',
                                    'buttonLabel' => 'Talk to sales',
                                    'href' => '/lead',
                                ],
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $agency = [
            'schemaVersion' => 1,
            'root' => [
                'id' => $id(),
                'type' => 'root',
                'version' => 1,
                'props' => [],
                'children' => [
                    [
                        'id' => $id(),
                        'type' => 'section',
                        'version' => 1,
                        'props' => [],
                        'children' => [
                            [
                                'id' => $id(),
                                'type' => 'hero',
                                'version' => 1,
                                'props' => [
                                    'eyebrow' => 'Digital agency',
                                    'title' => 'Design and build brands that convert',
                                    'subtitle' => 'Strategy, creative, and engineering under one roof.',
                                    'primaryCta' => ['label' => 'View work', 'href' => '/portfolio'],
                                    'secondaryCta' => ['label' => 'Hire us', 'href' => '/lead'],
                                ],
                                'children' => [],
                            ],
                        ],
                    ],
                    [
                        'id' => $id(),
                        'type' => 'section',
                        'version' => 1,
                        'props' => [],
                        'children' => [
                            [
                                'id' => $id(),
                                'type' => 'feature_grid',
                                'version' => 1,
                                'props' => [
                                    'heading' => 'Services',
                                    'items' => [
                                        ['title' => 'Brand', 'description' => 'Positioning, identity, and guidelines.', 'href' => '#'],
                                        ['title' => 'Web', 'description' => 'High-performance marketing sites.', 'href' => '#'],
                                        ['title' => 'Growth', 'description' => 'Experimentation and CRO programs.', 'href' => '#'],
                                    ],
                                ],
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $ecommerce = [
            'schemaVersion' => 1,
            'root' => [
                'id' => $id(),
                'type' => 'root',
                'version' => 1,
                'props' => [],
                'children' => [
                    [
                        'id' => $id(),
                        'type' => 'section',
                        'version' => 1,
                        'props' => [],
                        'children' => [
                            [
                                'id' => $id(),
                                'type' => 'hero',
                                'version' => 1,
                                'props' => [
                                    'eyebrow' => 'New collection',
                                    'title' => 'Products your customers will love',
                                    'subtitle' => 'Fast shipping, easy returns, premium quality.',
                                    'primaryCta' => ['label' => 'Shop now', 'href' => '/products'],
                                    'secondaryCta' => ['label' => 'Learn more', 'href' => '/about'],
                                ],
                                'children' => [],
                            ],
                        ],
                    ],
                    [
                        'id' => $id(),
                        'type' => 'section',
                        'version' => 1,
                        'props' => [],
                        'children' => [
                            [
                                'id' => $id(),
                                'type' => 'cta',
                                'version' => 1,
                                'props' => [
                                    'title' => 'Join the list',
                                    'body' => 'Get launches and limited drops first.',
                                    'buttonLabel' => 'Subscribe',
                                    'href' => '/lead',
                                ],
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $templates = [
            ['name' => 'SaaS landing', 'slug' => 'saas-landing', 'category' => 'saas', 'document_json' => $saas],
            ['name' => 'Agency landing', 'slug' => 'agency-landing', 'category' => 'agency', 'document_json' => $agency],
            ['name' => 'eCommerce hero', 'slug' => 'ecommerce-landing', 'category' => 'ecommerce', 'document_json' => $ecommerce],
        ];

        foreach ($templates as $t) {
            BuilderTemplate::query()->updateOrCreate(
                ['slug' => $t['slug']],
                [
                    'name' => $t['name'],
                    'category' => $t['category'],
                    'description' => null,
                    'document_json' => $t['document_json'],
                    'is_active' => true,
                    'sort_order' => 0,
                ]
            );
        }
    }
}
