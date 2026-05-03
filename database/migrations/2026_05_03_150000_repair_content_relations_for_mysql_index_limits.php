<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_relations')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $row = DB::selectOne(
            'SELECT
                MAX(CHAR_LENGTH(source_type)) AS max_source_type,
                MAX(CHAR_LENGTH(related_type)) AS max_related_type,
                MAX(CHAR_LENGTH(IFNULL(relation_type, \'\'))) AS max_relation_type
            FROM content_relations'
        );

        $maxSource = (int) ($row->max_source_type ?? 0);
        $maxRelated = (int) ($row->max_related_type ?? 0);
        $maxRelation = (int) ($row->max_relation_type ?? 0);

        if ($maxSource > 100 || $maxRelated > 100 || $maxRelation > 80) {
            throw new RuntimeException(
                "content_relations contains values exceeding VARCHAR limits after shortening (max source_type={$maxSource}, related_type={$maxRelated}, relation_type={$maxRelation}). Shorten data manually before migrating."
            );
        }

        $dups = DB::select(
            'SELECT source_type, source_id, related_type, related_id, relation_type, COUNT(*) AS total
            FROM content_relations
            GROUP BY source_type, source_id, related_type, related_id, relation_type
            HAVING total > 1'
        );

        if (count($dups) > 0) {
            $summary = json_encode($dups, JSON_THROW_ON_ERROR);

            throw new RuntimeException(
                "content_relations has duplicate rows for the unique tuple. Resolve duplicates before adding unique index. Groups: {$summary}"
            );
        }

        DB::statement(
            'ALTER TABLE content_relations
            MODIFY source_type VARCHAR(100) NOT NULL,
            MODIFY related_type VARCHAR(100) NOT NULL,
            MODIFY relation_type VARCHAR(80) NULL'
        );

        $indexNames = $this->contentRelationsIndexNames();

        if (! in_array('content_relations_unique_link', $indexNames, true)) {
            DB::statement(
                'ALTER TABLE content_relations
                ADD UNIQUE KEY content_relations_unique_link (source_type, source_id, related_type, related_id, relation_type)'
            );
            $indexNames = $this->contentRelationsIndexNames();
        }

        if (! in_array('content_relations_source_index', $indexNames, true)) {
            DB::statement(
                'ALTER TABLE content_relations ADD INDEX content_relations_source_index (source_type, source_id)'
            );
            $indexNames = $this->contentRelationsIndexNames();
        }

        if (! in_array('content_relations_related_index', $indexNames, true)) {
            DB::statement(
                'ALTER TABLE content_relations ADD INDEX content_relations_related_index (related_type, related_id)'
            );
            $indexNames = $this->contentRelationsIndexNames();
        }

        if (! in_array('content_relations_relation_type_index', $indexNames, true)) {
            DB::statement(
                'ALTER TABLE content_relations ADD INDEX content_relations_relation_type_index (relation_type)'
            );
        }
    }

    /**
     * @return list<string>
     */
    private function contentRelationsIndexNames(): array
    {
        $rows = DB::select('SHOW INDEX FROM content_relations');

        return collect($rows)
            ->pluck('Key_name')
            ->unique()
            ->values()
            ->map(static fn ($name): string => (string) $name)
            ->all();
    }

    public function down(): void
    {
        //
    }
};
