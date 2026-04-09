<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;

class PublishScheduledPagesCommand extends Command
{
    protected $signature = 'pages:publish-scheduled';

    protected $description = 'Publish pages whose scheduled_publish_at is due';

    public function handle(): int
    {
        $q = Page::query()
            ->whereNotNull('scheduled_publish_at')
            ->where('scheduled_publish_at', '<=', now())
            ->where('status', 'draft');

        $count = 0;
        foreach ($q->cursor() as $page) {
            $page->update([
                'status' => 'published',
                'published_at' => now(),
                'scheduled_publish_at' => null,
            ]);
            $count++;
        }

        $this->info("Published {$count} page(s).");

        return self::SUCCESS;
    }
}
