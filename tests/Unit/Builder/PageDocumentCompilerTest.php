<?php

namespace Tests\Unit\Builder;

use App\Services\Builder\BuilderEmbedSanitizer;
use App\Services\Builder\PageDocumentCompiler;
use App\Support\Builder\BuilderDocumentDefaults;
use PHPUnit\Framework\TestCase;

class PageDocumentCompilerTest extends TestCase
{
    public function test_compiles_hero_and_cta(): void
    {
        $compiler = new PageDocumentCompiler(new BuilderEmbedSanitizer);
        $doc = BuilderDocumentDefaults::emptyDocument();
        $doc['root']['children'] = [
            [
                'id' => 'a',
                'type' => 'hero',
                'version' => 1,
                'props' => [
                    'eyebrow' => 'E',
                    'title' => 'T',
                    'subtitle' => 'S',
                    'primaryCta' => ['label' => 'P', 'href' => '/p'],
                    'secondaryCta' => ['label' => 'Q', 'href' => '/q'],
                ],
                'children' => [],
            ],
            [
                'id' => 'b',
                'type' => 'cta',
                'version' => 1,
                'props' => [
                    'title' => 'C',
                    'body' => 'B',
                    'buttonLabel' => 'Go',
                    'href' => '/c',
                ],
                'children' => [],
            ],
        ];

        $blocks = $compiler->compileToBlocks($doc);
        $this->assertCount(2, $blocks);
        $this->assertSame('hero', $blocks[0]['type']);
        $this->assertSame('T', $blocks[0]['data']['title']);
        $this->assertSame('cta', $blocks[1]['type']);
    }
}
