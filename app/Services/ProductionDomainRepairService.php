<?php

namespace App\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Idempotent repair: replaces legacy artixcore.test / hello@artixcore.test in app-owned CMS/settings tables.
 * Does not touch lead or contact_message email address columns.
 */
final class ProductionDomainRepairService
{
    /** @var array<string, int> */
    private array $rowUpdatesByTable = [];

    private int $rowsUpdated = 0;

    public function replaceString(string $value): string
    {
        $out = $value;
        $pairs = [
            'mailto:hello@artixcore.test' => 'mailto:hello@artixcore.com',
            'hello@artixcore.test' => 'hello@artixcore.com',
            'https://artixcore.test' => 'https://artixcore.com',
            'http://artixcore.test' => 'https://artixcore.com',
            'artixcore.test' => 'artixcore.com',
        ];
        foreach ($pairs as $from => $to) {
            $out = str_replace($from, $to, $out);
        }

        return $out;
    }

    public function stringNeedsRepair(?string $value): bool
    {
        return is_string($value) && str_contains($value, 'artixcore.test');
    }

    /**
     * @return array{0: mixed, 1: bool}
     */
    public function replaceInMixed(mixed $data): array
    {
        if (is_string($data)) {
            $new = $this->replaceString($data);

            return [$new, $new !== $data];
        }

        if (is_array($data)) {
            $changed = false;
            $out = [];
            foreach ($data as $k => $v) {
                [$nv, $c] = $this->replaceInMixed($v);
                $out[$k] = $nv;
                if ($c) {
                    $changed = true;
                }
            }

            return [$out, $changed];
        }

        return [$data, false];
    }

    /**
     * @return array{rows_updated: int, by_table: array<string, int>, messages: list<string>}
     */
    public function run(?Command $command = null): array
    {
        $this->rowUpdatesByTable = [];
        $this->rowsUpdated = 0;
        $messages = [];

        foreach ($this->tableDefinitions() as $def) {
            $table = $def['table'];
            if (! Schema::hasTable($table)) {
                $messages[] = "Skip (no table): {$table}";
                $this->line($command, "Skip (no table): {$table}");

                continue;
            }

            try {
                if (! empty($def['strings'])) {
                    $this->repairStringLikeColumns($table, $def['strings'], $command);
                }
                if (! empty($def['texts'])) {
                    $this->repairStringLikeColumns($table, $def['texts'], $command);
                }
                if (! empty($def['json'])) {
                    $this->repairJsonColumns($table, $def['json'], $command);
                }
            } catch (\Throwable $e) {
                $msg = "Error repairing {$table}: {$e->getMessage()}";
                $messages[] = $msg;
                $this->line($command, $msg);
            }
        }

        $summary = [
            'rows_updated' => $this->rowsUpdated,
            'by_table' => $this->rowUpdatesByTable,
            'messages' => $messages,
        ];

        $this->line($command, 'Total rows updated: '.$this->rowsUpdated);

        return $summary;
    }

    /**
     * @param  list<string>  $columns
     */
    private function repairStringLikeColumns(string $table, array $columns, ?Command $command): void
    {
        $present = [];
        foreach ($columns as $col) {
            if (Schema::hasColumn($table, $col)) {
                $present[] = $col;
            }
        }

        if ($present === []) {
            return;
        }

        $checked = implode(', ', $present);
        $this->line($command, "Checking {$table}: {$checked}");

        DB::table($table)->orderBy('id')->chunkById(100, function ($rows) use ($table, $present): void {
            foreach ($rows as $row) {
                $updates = [];
                foreach ($present as $col) {
                    $v = $row->{$col} ?? null;
                    if ($this->stringNeedsRepair(is_string($v) ? $v : null)) {
                        $updates[$col] = $this->replaceString((string) $v);
                    }
                }

                if ($updates === []) {
                    continue;
                }

                DB::table($table)->where('id', $row->id)->update($updates);
                $this->bumpTable($table);
            }
        }, 'id');
    }

    /**
     * @param  list<string>  $columns
     */
    private function repairJsonColumns(string $table, array $columns, ?Command $command): void
    {
        $present = [];
        foreach ($columns as $col) {
            if (Schema::hasColumn($table, $col)) {
                $present[] = $col;
            }
        }

        if ($present === []) {
            return;
        }

        $checked = implode(', ', $present);
        $this->line($command, "Checking {$table} JSON: {$checked}");

        DB::table($table)->orderBy('id')->chunkById(50, function ($rows) use ($table, $present): void {
            foreach ($rows as $row) {
                $updates = [];
                foreach ($present as $col) {
                    $raw = $row->{$col} ?? null;
                    if ($raw === null || $raw === '') {
                        continue;
                    }

                    if (is_array($raw)) {
                        $decoded = $raw;
                    } elseif (is_string($raw)) {
                        $decoded = json_decode($raw, true);
                        if (! is_array($decoded)) {
                            continue;
                        }
                    } else {
                        continue;
                    }

                    [$next, $changed] = $this->replaceInMixed($decoded);
                    if ($changed) {
                        $updates[$col] = $next;
                    }
                }

                if ($updates === []) {
                    continue;
                }

                DB::table($table)->where('id', $row->id)->update($updates);
                $this->bumpTable($table);
            }
        }, 'id');
    }

    private function bumpTable(string $table): void
    {
        $this->rowsUpdated++;
        $this->rowUpdatesByTable[$table] = ($this->rowUpdatesByTable[$table] ?? 0) + 1;
    }

    private function line(?Command $command, string $message): void
    {
        if ($command !== null) {
            $command->line($message);
        }
    }

    /**
     * Table/column map derived from migrations — CMS/settings only; excludes leads/contact_messages identity emails.
     *
     * @return list<array{table: string, strings?: list<string>, texts?: list<string>, json?: list<string>}>
     */
    private function tableDefinitions(): array
    {
        return [
            [
                'table' => 'site_settings',
                'strings' => ['contact_email', 'default_meta_title', 'site_name'],
                'texts' => ['default_meta_description'],
                'json' => ['social_links', 'design_tokens', 'homepage_content', 'about_content', 'services_page_content', 'saas_page_content'],
            ],
            [
                'table' => 'seo_settings',
                'texts' => ['value'],
            ],
            [
                'table' => 'pages',
                'strings' => ['meta_title'],
                'texts' => ['meta_description', 'custom_head_html', 'custom_body_html'],
                'json' => ['meta', 'builder_settings_json'],
            ],
            [
                'table' => 'page_blocks',
                'json' => ['data'],
            ],
            [
                'table' => 'page_versions',
                'json' => ['document_json'],
            ],
            [
                'table' => 'builder_templates',
                'strings' => ['name', 'slug', 'category', 'description'],
                'json' => ['document_json'],
            ],
            [
                'table' => 'builder_saved_sections',
                'strings' => ['name'],
                'json' => ['document_json'],
            ],
            [
                'table' => 'nav_items',
                'strings' => ['label', 'url'],
                'json' => ['feature_payload'],
            ],
            [
                'table' => 'services',
                'strings' => ['title', 'summary', 'slug'],
                'texts' => ['body'],
                'json' => ['meta'],
            ],
            [
                'table' => 'testimonials',
                'strings' => ['author_name', 'role', 'company'],
                'texts' => ['body'],
            ],
            [
                'table' => 'faqs',
                'strings' => ['question'],
                'texts' => ['answer'],
            ],
            [
                'table' => 'legal_pages',
                'strings' => ['slug', 'title', 'meta_title'],
                'texts' => ['body', 'meta_description'],
            ],
            [
                'table' => 'job_postings',
                'strings' => ['title', 'location', 'employment_type'],
                'texts' => ['body'],
            ],
            [
                'table' => 'articles',
                'strings' => ['slug', 'title', 'summary', 'meta_title'],
                'texts' => ['body', 'meta_description'],
            ],
            [
                'table' => 'case_studies',
                'strings' => ['slug', 'title', 'client_name', 'summary', 'meta_title'],
                'texts' => ['body', 'meta_description'],
            ],
            [
                'table' => 'research_papers',
                'strings' => ['slug', 'title', 'summary', 'meta_title'],
                'texts' => ['body', 'meta_description'],
            ],
            [
                'table' => 'products',
                'strings' => ['slug', 'title', 'tagline', 'summary', 'meta_title'],
                'texts' => ['body', 'meta_description'],
            ],
            [
                'table' => 'team_profiles',
                'strings' => ['slug', 'name', 'role', 'avatar_url'],
                'texts' => ['bio'],
            ],
            [
                'table' => 'ai_builder_business_profiles',
                'strings' => ['business_name', 'business_type', 'tone_of_voice', 'location', 'preferred_cta_goal', 'writing_style'],
                'texts' => ['brand_summary', 'target_audience', 'main_services', 'unique_selling_points', 'offer_details', 'forbidden_topics', 'style_notes'],
                'json' => ['contact_details', 'brand_colors'],
            ],
            [
                'table' => 'micro_tool_settings',
                'texts' => ['value'],
            ],
        ];
    }
}
