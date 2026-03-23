<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\NavItem;
use App\Models\NavMenu;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Product;
use App\Models\ResearchPaper;
use App\Models\Taxonomy;
use App\Models\TeamProfile;
use App\Models\Term;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $cat = Taxonomy::query()->create(['slug' => 'categories', 'name' => 'Categories']);
        $topic = Taxonomy::query()->create(['slug' => 'topics', 'name' => 'Topics']);
        $tag = Taxonomy::query()->create(['slug' => 'tags', 'name' => 'Tags']);

        $tSaaS = Term::query()->create(['taxonomy_id' => $cat->id, 'slug' => 'saas', 'name' => 'SaaS', 'sort_order' => 1]);
        $tChain = Term::query()->create(['taxonomy_id' => $cat->id, 'slug' => 'blockchain', 'name' => 'Blockchain', 'sort_order' => 2]);
        $tQuantum = Term::query()->create(['taxonomy_id' => $cat->id, 'slug' => 'quantum', 'name' => 'Quantum', 'sort_order' => 3]);

        Term::query()->create(['taxonomy_id' => $topic->id, 'slug' => 'innovation', 'name' => 'Innovation', 'sort_order' => 1]);
        Term::query()->create(['taxonomy_id' => $topic->id, 'slug' => 'engineering', 'name' => 'Engineering', 'sort_order' => 2]);

        Term::query()->create(['taxonomy_id' => $tag->id, 'slug' => 'solana', 'name' => 'Solana', 'sort_order' => 1]);
        Term::query()->create(['taxonomy_id' => $tag->id, 'slug' => 'spl-token', 'name' => 'SPL Token', 'sort_order' => 2]);

        $home = Page::query()->create([
            'parent_id' => null,
            'path' => 'home',
            'title' => 'Home',
            'meta_title' => 'Artixcore — Technology that scales',
            'meta_description' => 'SaaS, blockchain, quantum, and research-driven software.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $products = Page::query()->create([
            'parent_id' => null,
            'path' => 'products',
            'title' => 'Products',
            'status' => 'published',
            'published_at' => now(),
        ]);

        foreach (
            [
                ['saas', 'SaaS platforms'],
                ['blockchain', 'Blockchain'],
                ['quantum', 'Quantum computing'],
            ] as [$slug, $title]
        ) {
            Page::query()->create([
                'parent_id' => $products->id,
                'path' => 'products/'.$slug,
                'title' => $title,
                'status' => 'published',
                'published_at' => now(),
            ]);
        }

        $solutions = Page::query()->create([
            'parent_id' => null,
            'path' => 'solutions',
            'title' => 'Solutions',
            'status' => 'published',
            'published_at' => now(),
        ]);

        foreach (
            [
                ['enterprise-saas', 'Enterprise SaaS'],
                ['web3', 'Web3 and tokens'],
                ['research-platforms', 'R&D platforms'],
            ] as [$slug, $title]
        ) {
            Page::query()->create([
                'parent_id' => $solutions->id,
                'path' => 'solutions/'.$slug,
                'title' => $title,
                'status' => 'published',
                'published_at' => now(),
            ]);
        }

        foreach (
            [
                ['research', 'Research'],
                ['resources', 'Resources'],
                ['company', 'Company'],
            ] as [$path, $title]
        ) {
            Page::query()->create([
                'parent_id' => null,
                'path' => $path,
                'title' => $title,
                'status' => 'published',
                'published_at' => now(),
            ]);
        }

        foreach (
            [
                ['about', 'About'],
                ['team', 'Team'],
                ['contact', 'Contact'],
            ] as [$path, $title]
        ) {
            Page::query()->create([
                'parent_id' => null,
                'path' => $path,
                'title' => $title,
                'status' => 'published',
                'published_at' => now(),
            ]);
        }

        Page::query()->create([
            'parent_id' => null,
            'path' => 'resources/articles',
            'title' => 'Articles & insights',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Page::query()->create([
            'parent_id' => null,
            'path' => 'resources/case-studies',
            'title' => 'Case studies',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Page::query()->create([
            'parent_id' => null,
            'path' => 'research/papers',
            'title' => 'Research papers',
            'status' => 'published',
            'published_at' => now(),
        ]);

        PageBlock::query()->create([
            'page_id' => $home->id,
            'sort_order' => 0,
            'type' => 'hero',
            'data' => [
                'eyebrow' => 'Artixcore',
                'title' => 'Software that scales with ambition',
                'subtitle' => 'SaaS platforms, blockchain systems, quantum-ready tooling, and research-led engineering.',
                'primaryCta' => ['label' => 'Book a call', 'href' => '/contact'],
                'secondaryCta' => ['label' => 'Explore products', 'href' => '/products'],
            ],
        ]);

        PageBlock::query()->create([
            'page_id' => $home->id,
            'sort_order' => 1,
            'type' => 'feature_grid',
            'data' => [
                'heading' => 'What we build',
                'items' => [
                    ['title' => 'SaaS platforms', 'description' => 'Multi-tenant products with enterprise reliability.', 'href' => '/products/saas'],
                    ['title' => 'Blockchain', 'description' => 'Including Solana SPL token programs and integrations.', 'href' => '/products/blockchain'],
                    ['title' => 'Quantum-ready tools', 'description' => 'Pipelines and interfaces for emerging quantum workloads.', 'href' => '/products/quantum'],
                ],
            ],
        ]);

        PageBlock::query()->create([
            'page_id' => $home->id,
            'sort_order' => 2,
            'type' => 'cta',
            'data' => [
                'title' => 'Ready to ship?',
                'body' => 'Tell us about your product, research goals, or infrastructure needs.',
                'buttonLabel' => 'Contact us',
                'href' => '/contact',
            ],
        ]);

        $primary = NavMenu::query()->create(['key' => 'primary', 'name' => 'Primary header']);
        $footerMenu = NavMenu::query()->create(['key' => 'footer', 'name' => 'Footer']);

        foreach (
            [
                ['Products', '/products'],
                ['Solutions', '/solutions'],
                ['Research', '/research'],
                ['Articles', '/resources/articles'],
                ['Case studies', '/resources/case-studies'],
                ['About', '/about'],
                ['Team', '/team'],
                ['Contact', '/contact'],
            ] as $i => [$label, $url]
        ) {
            NavItem::query()->create([
                'nav_menu_id' => $footerMenu->id,
                'parent_id' => null,
                'label' => $label,
                'url' => $url,
                'sort_order' => $i,
            ]);
        }

        $microToolsItem = NavItem::query()->create([
            'nav_menu_id' => $primary->id,
            'parent_id' => null,
            'label' => 'Micro Tools',
            'url' => '/micro-tools',
            'page_id' => null,
            'sort_order' => 0,
        ]);

        foreach (
            [
                ['All tools', '/micro-tools'],
                ['Web tools', '/micro-tools/web'],
                ['Domain & DNS', '/micro-tools/domain-dns'],
                ['Security & trust', '/micro-tools/security-trust'],
                ['Media', '/micro-tools/media'],
                ['SEO & content', '/micro-tools/seo-content'],
                ['Developer', '/micro-tools/developer'],
                ['Marketing', '/micro-tools/marketing'],
                ['Favorites & history', '/micro-tools/me'],
            ] as $i => [$label, $url]
        ) {
            NavItem::query()->create([
                'nav_menu_id' => $primary->id,
                'parent_id' => $microToolsItem->id,
                'label' => $label,
                'url' => $url,
                'sort_order' => $i,
            ]);
        }

        $productsItem = NavItem::query()->create([
            'nav_menu_id' => $primary->id,
            'parent_id' => null,
            'label' => 'Products',
            'url' => null,
            'page_id' => Page::query()->where('path', 'products')->value('id'),
            'sort_order' => 1,
            'feature_payload' => [
                'title' => 'Product portfolio',
                'description' => 'Platforms and tools across SaaS, chain, and quantum.',
                'href' => '/products',
            ],
        ]);

        foreach (
            [
                ['SaaS', 'products/saas'],
                ['Blockchain', 'products/blockchain'],
                ['Quantum', 'products/quantum'],
            ] as $i => [$label, $path]
        ) {
            NavItem::query()->create([
                'nav_menu_id' => $primary->id,
                'parent_id' => $productsItem->id,
                'label' => $label,
                'page_id' => Page::query()->where('path', $path)->value('id'),
                'sort_order' => $i,
            ]);
        }

        $solutions = NavItem::query()->create([
            'nav_menu_id' => $primary->id,
            'parent_id' => null,
            'label' => 'Solutions',
            'page_id' => Page::query()->where('path', 'solutions')->value('id'),
            'sort_order' => 2,
        ]);

        foreach (
            [
                ['Enterprise SaaS', '/solutions/enterprise-saas'],
                ['Web3 & tokens', '/solutions/web3'],
                ['R&D platforms', '/solutions/research-platforms'],
            ] as $i => [$label, $url]
        ) {
            NavItem::query()->create([
                'nav_menu_id' => $primary->id,
                'parent_id' => $solutions->id,
                'label' => $label,
                'url' => $url,
                'sort_order' => $i,
            ]);
        }

        $research = NavItem::query()->create([
            'nav_menu_id' => $primary->id,
            'parent_id' => null,
            'label' => 'Research',
            'page_id' => Page::query()->where('path', 'research')->value('id'),
            'sort_order' => 3,
        ]);

        NavItem::query()->create([
            'nav_menu_id' => $primary->id,
            'parent_id' => $research->id,
            'label' => 'Papers',
            'page_id' => Page::query()->where('path', 'research/papers')->value('id'),
            'sort_order' => 0,
        ]);

        $company = NavItem::query()->create([
            'nav_menu_id' => $primary->id,
            'parent_id' => null,
            'label' => 'Company',
            'page_id' => Page::query()->where('path', 'company')->value('id'),
            'sort_order' => 4,
        ]);

        foreach (
            [
                ['About', 'about'],
                ['Team', 'team'],
                ['Contact', 'contact'],
            ] as $i => [$label, $path]
        ) {
            NavItem::query()->create([
                'nav_menu_id' => $primary->id,
                'parent_id' => $company->id,
                'label' => $label,
                'page_id' => Page::query()->where('path', $path)->value('id'),
                'sort_order' => $i,
            ]);
        }

        $resources = NavItem::query()->create([
            'nav_menu_id' => $primary->id,
            'parent_id' => null,
            'label' => 'Resources',
            'page_id' => Page::query()->where('path', 'resources')->value('id'),
            'sort_order' => 5,
        ]);

        NavItem::query()->create([
            'nav_menu_id' => $primary->id,
            'parent_id' => $resources->id,
            'label' => 'Articles',
            'page_id' => Page::query()->where('path', 'resources/articles')->value('id'),
            'sort_order' => 0,
        ]);

        NavItem::query()->create([
            'nav_menu_id' => $primary->id,
            'parent_id' => $resources->id,
            'label' => 'Case studies',
            'page_id' => Page::query()->where('path', 'resources/case-studies')->value('id'),
            'sort_order' => 1,
        ]);

        $article = Article::query()->create([
            'slug' => 'building-spl-token-programs',
            'title' => 'Building reliable SPL token programs',
            'summary' => 'Patterns we use for Solana token lifecycle, metadata, and upgrades.',
            'body' => "## Overview\n\nArtixcore ships SPL-compatible programs with clear authority models and test coverage.",
            'status' => 'published',
            'featured' => true,
            'trending_score' => 10,
            'published_at' => now(),
        ]);
        $article->terms()->attach([$tChain->id, $tSaaS->id]);

        ResearchPaper::query()->create([
            'slug' => 'quantum-orchestration-note',
            'title' => 'Note on quantum workload orchestration',
            'summary' => 'Draft framework for hybrid classical-quantum pipelines.',
            'body' => 'Abstract: we outline scheduling constraints for near-term devices.',
            'status' => 'published',
            'featured' => true,
            'trending_score' => 5,
            'published_at' => now(),
        ]);

        CaseStudy::query()->create([
            'slug' => 'fintech-saas-rollout',
            'title' => 'Multi-tenant SaaS for a fintech scale-up',
            'client_name' => 'Example FinCo',
            'summary' => 'Latency, compliance, and onboarding in eight weeks.',
            'body' => 'We delivered core billing APIs, admin console, and audit trails.',
            'status' => 'published',
            'featured' => true,
            'published_at' => now(),
        ]);

        foreach (
            [
                ['artixcore-cloud', 'Artixcore Cloud', 'Composable SaaS foundation'],
                ['chain-kit', 'Chain Kit', 'Solana SPL and wallet utilities'],
                ['q-pipeline', 'Q-Pipeline', 'Research-oriented quantum job interfaces'],
            ] as [$slug, $title, $tagline]
        ) {
            Product::query()->create([
                'slug' => $slug,
                'title' => $title,
                'tagline' => $tagline,
                'summary' => 'Product summary placeholder.',
                'body' => 'Details managed in CMS.',
                'status' => 'published',
                'featured' => true,
                'published_at' => now(),
            ]);
        }

        TeamProfile::query()->create([
            'slug' => 'jane-doe',
            'name' => 'Jane Doe',
            'role' => 'CTO',
            'bio' => 'Leads platform architecture and research partnerships.',
            'status' => 'published',
            'sort_order' => 0,
            'published_at' => now(),
        ]);
    }
}
