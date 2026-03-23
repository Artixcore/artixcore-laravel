<?php

namespace App\Services\Tools\Support;

class HtmlPageAnalyzer
{
    /**
     * @return array{title: ?string, meta_description: ?string, canonical: ?string, og: array<string, string>, twitter: array<string, string>, viewport: ?string, charset: ?string, h1_sample: list<string>}
     */
    public static function analyze(string $html): array
    {
        $title = self::matchOne('/<title[^>]*>(.*?)<\/title>/is', $html);
        $metaDescription = self::metaContent($html, 'name', 'description');
        $canonical = self::linkRel($html, 'canonical');
        $viewport = self::metaContent($html, 'name', 'viewport');

        $og = [];
        foreach (['og:title', 'og:description', 'og:image', 'og:url', 'og:type'] as $prop) {
            $v = self::metaProperty($html, $prop);
            if ($v !== null && $v !== '') {
                $og[$prop] = $v;
            }
        }

        $twitter = [];
        foreach (['twitter:card', 'twitter:title', 'twitter:description', 'twitter:image'] as $prop) {
            $v = self::metaNameOrProperty($html, $prop);
            if ($v !== null && $v !== '') {
                $twitter[$prop] = $v;
            }
        }

        $charset = null;
        if (preg_match('/<meta[^>]+charset=["\']?([^"\'\s>]+)/i', $html, $m)) {
            $charset = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $h1s = [];
        if (preg_match_all('/<h1[^>]*>(.*?)<\/h1>/is', $html, $mh)) {
            foreach ($mh[1] as $h) {
                $t = trim(html_entity_decode(strip_tags($h), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                if ($t !== '') {
                    $h1s[] = mb_substr($t, 0, 200);
                }
                if (count($h1s) >= 5) {
                    break;
                }
            }
        }

        return [
            'title' => $title !== '' ? $title : null,
            'meta_description' => $metaDescription !== '' ? $metaDescription : null,
            'canonical' => $canonical !== '' ? $canonical : null,
            'og' => $og,
            'twitter' => $twitter,
            'viewport' => $viewport !== '' ? $viewport : null,
            'charset' => $charset,
            'h1_sample' => $h1s,
        ];
    }

    /**
     * @return list<array{type: string, raw: string}>
     */
    public static function jsonLdBlocks(string $html): array
    {
        $out = [];
        if (! preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $m)) {
            return $out;
        }
        foreach ($m[1] as $raw) {
            $trim = trim($raw);
            if ($trim === '') {
                continue;
            }
            $decoded = json_decode($trim, true);
            $type = 'unknown';
            if (is_array($decoded)) {
                if (isset($decoded['@type'])) {
                    $type = is_array($decoded['@type']) ? implode(', ', $decoded['@type']) : (string) $decoded['@type'];
                } elseif (isset($decoded[0]) && is_array($decoded[0]) && isset($decoded[0]['@type'])) {
                    $type = is_array($decoded[0]['@type']) ? 'multiple' : (string) $decoded[0]['@type'];
                }
            }
            $out[] = ['type' => $type, 'raw' => mb_substr($trim, 0, 2000)];
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    public static function technologyHints(string $html): array
    {
        $hints = [];
        $pairs = [
            'WordPress' => '/wp-content\/|wp-includes\//i',
            'WooCommerce' => '/woocommerce/i',
            'Shopify' => '/cdn\.shopify\.com/i',
            'Drupal' => '/\/sites\/default\/files|drupal\.js/i',
            'Next.js' => '/__NEXT_DATA__|_next\/static/i',
            'React' => '/react(?:\.min)?\.js|data-reactroot/i',
            'Vue.js' => '/vue(?:\.min)?\.js|__vue__/i',
            'Angular' => '/ng-version|angular\.js/i',
            'Google Analytics' => '/google-analytics\.com|googletagmanager\.com\/gtag/i',
            'Cloudflare' => '/cf-ray|__cf_bm/i',
            'jQuery' => '/jquery(?:\.min)?\.js/i',
            'Bootstrap' => '/bootstrap(?:\.min)?\.(?:css|js)/i',
            'Tailwind (hint)' => '/tailwind/i',
        ];
        foreach ($pairs as $label => $pattern) {
            if (preg_match($pattern, $html)) {
                $hints[] = $label;
            }
        }

        return $hints;
    }

    private static function matchOne(string $pattern, string $html): string
    {
        if (! preg_match($pattern, $html, $m)) {
            return '';
        }

        return trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private static function metaContent(string $html, string $attr, string $value): string
    {
        $q = preg_quote($value, '/');
        if (preg_match('/<meta[^>]+'.$attr.'=["\']'.$q.'["\'][^>]+content=["\']([^"\']*)["\']/i', $html, $m)) {
            return trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }
        if (preg_match('/<meta[^>]+content=["\']([^"\']*)["\'][^>]+'.$attr.'=["\']'.$q.'["\']/i', $html, $m)) {
            return trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        return '';
    }

    private static function metaProperty(string $html, string $property): ?string
    {
        $q = preg_quote($property, '/');
        if (preg_match('/<meta[^>]+property=["\']'.$q.'["\'][^>]+content=["\']([^"\']*)["\']/i', $html, $m)) {
            return trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }
        if (preg_match('/<meta[^>]+content=["\']([^"\']*)["\'][^>]+property=["\']'.$q.'["\']/i', $html, $m)) {
            return trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        return null;
    }

    private static function metaNameOrProperty(string $html, string $name): ?string
    {
        $q = preg_quote($name, '/');
        if (preg_match('/<meta[^>]+(?:name|property)=["\']'.$q.'["\'][^>]+content=["\']([^"\']*)["\']/i', $html, $m)) {
            return trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }
        if (preg_match('/<meta[^>]+content=["\']([^"\']*)["\'][^>]+(?:name|property)=["\']'.$q.'["\']/i', $html, $m)) {
            return trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        return null;
    }

    private static function linkRel(string $html, string $rel): string
    {
        $q = preg_quote($rel, '/');
        if (preg_match('/<link[^>]+rel=["\']'.$q.'["\'][^>]+href=["\']([^"\']*)["\']/i', $html, $m)) {
            return trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }
        if (preg_match('/<link[^>]+href=["\']([^"\']*)["\'][^>]+rel=["\']'.$q.'["\']/i', $html, $m)) {
            return trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        return '';
    }
}
