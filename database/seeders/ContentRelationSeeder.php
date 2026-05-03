<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ContentRelation;
use App\Models\Faq;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ContentRelationSeeder extends Seeder
{
    public function run(): void
    {
        $this->attachFaqsToAppDevelopmentService();

        $service = Service::query()->where('slug', 'web-development')->first();
        $article = Article::query()->published()->orderByDesc('published_at')->first();
        if ($service !== null && $article !== null) {
            ContentRelation::query()->firstOrCreate(
                [
                    'source_type' => Service::class,
                    'source_id' => $service->id,
                    'related_type' => Article::class,
                    'related_id' => $article->id,
                    'relation_type' => ContentRelation::RELATED_ARTICLE,
                ],
                ['sort_order' => 0, 'is_featured' => false],
            );
        }
    }

    private function attachFaqsToAppDevelopmentService(): void
    {
        $service = Service::query()->where('slug', 'app-development')->first();
        if ($service === null) {
            return;
        }

        $faqs = Faq::query()->published()->orderBy('sort_order')->orderBy('id')->get();
        if ($faqs->isEmpty()) {
            return;
        }

        $sync = [];
        foreach ($faqs->values() as $index => $faq) {
            $sync[$faq->id] = ['sort_order' => $index];
        }
        $service->faqs()->sync($sync);
    }
}
