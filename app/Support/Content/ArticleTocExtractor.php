<?php

namespace App\Support\Content;

use Illuminate\Support\Str;

class ArticleTocExtractor
{
    /**
     * Inject stable anchor ids into h2/h3 tags and return TOC entries.
     *
     * @return array{html: string, toc: list<array{id: string, level: int, text: string}>}
     */
    public static function injectAnchorIds(string $html): array
    {
        $toc = [];
        $html = preg_replace_callback(
            '/<h([23])(\s[^>]*)?>(.*?)<\/h\1>/is',
            function (array $m) use (&$toc): string {
                $level = (int) $m[1];
                $attrs = $m[2] ?? '';
                $inner = $m[3];
                $text = trim(preg_replace('/\s+/', ' ', strip_tags($inner)) ?? '');
                if ($text === '') {
                    return '<h'.$level.$attrs.'>'.$inner.'</h'.$level.'>';
                }
                $base = Str::slug(Str::limit($text, 80, ''));
                if ($base === '') {
                    $base = 'section';
                }
                $id = 'toc-'.$base.'-'.substr(md5($text.(string) count($toc)), 0, 8);
                $toc[] = ['id' => $id, 'level' => $level, 'text' => $text];

                if (preg_match('/\bid\s*=\s*["\'][^"\']*["\']/i', $attrs)) {
                    return '<h'.$level.$attrs.'>'.$inner.'</h'.$level.'>';
                }

                return '<h'.$level.' id="'.htmlspecialchars($id, ENT_QUOTES | ENT_HTML5, 'UTF-8').'"'.$attrs.'>'.$inner.'</h'.$level.'>';
            },
            $html
        ) ?? $html;

        return ['html' => $html, 'toc' => $toc];
    }
}
