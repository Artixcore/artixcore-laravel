<?php

namespace Database\Seeders;

use App\Models\MicroTool;
use App\Models\MicroToolCategory;
use Illuminate\Database\Seeder;

class MicroToolsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $rows = [
            ['slug' => 'website-technology', 'category' => 'web', 'title' => 'Website technology checker', 'description' => 'Detect common technologies from a public page (single fetch, heuristic).', 'execution_mode' => 'server', 'sort_order' => 10, 'is_popular' => true, 'input_schema' => ['fields' => [['name' => 'url', 'type' => 'url', 'label' => 'Website URL', 'required' => true]]]],
            ['slug' => 'website-audit-basic', 'category' => 'web', 'title' => 'Basic website audit', 'description' => 'Lightweight health check: title, meta, response headers.', 'execution_mode' => 'server', 'sort_order' => 20, 'input_schema' => ['fields' => [['name' => 'url', 'type' => 'url', 'label' => 'Page URL', 'required' => true]]]],
            ['slug' => 'speed-snapshot', 'category' => 'web', 'title' => 'Website speed snapshot', 'description' => 'TTFB, timing, and response size for a single URL.', 'execution_mode' => 'server', 'sort_order' => 30, 'input_schema' => ['fields' => [['name' => 'url', 'type' => 'url', 'label' => 'URL', 'required' => true]]]],
            ['slug' => 'uptime-check', 'category' => 'web', 'title' => 'Uptime checker', 'description' => 'Check if a URL responds and how long it takes.', 'execution_mode' => 'server', 'sort_order' => 40, 'is_popular' => true, 'limits' => ['guest_per_minute' => 20, 'auth_per_minute' => 100], 'input_schema' => ['fields' => [['name' => 'url', 'type' => 'url', 'label' => 'URL', 'required' => true]]]],
            ['slug' => 'mobile-friendly-hints', 'category' => 'web', 'title' => 'Mobile-friendly hints', 'description' => 'Viewport and basic mobile-readiness signals from HTML.', 'execution_mode' => 'server', 'sort_order' => 50, 'input_schema' => ['fields' => [['name' => 'url', 'type' => 'url', 'label' => 'Page URL', 'required' => true]]]],
            ['slug' => 'broken-link-checker', 'category' => 'web', 'title' => 'Broken link checker', 'description' => 'Scan links on a page (rate-limited, capped depth).', 'execution_mode' => 'server', 'sort_order' => 60, 'is_new' => true, 'input_schema' => ['fields' => [['name' => 'url', 'type' => 'url', 'label' => 'Page URL', 'required' => true]]]],
            ['slug' => 'whois-lookup', 'category' => 'domain-dns', 'title' => 'WHOIS lookup', 'description' => 'Public registration data where available.', 'execution_mode' => 'server', 'sort_order' => 70, 'input_schema' => ['fields' => [['name' => 'domain', 'type' => 'text', 'label' => 'Domain', 'required' => true]]]],
            ['slug' => 'dns-lookup', 'category' => 'domain-dns', 'title' => 'DNS lookup', 'description' => 'A, AAAA, MX, TXT, NS, CNAME and more for a hostname.', 'execution_mode' => 'server', 'sort_order' => 80, 'is_popular' => true, 'input_schema' => ['fields' => [['name' => 'hostname', 'type' => 'text', 'label' => 'Hostname', 'placeholder' => 'example.com', 'required' => true]]]],
            ['slug' => 'ssl-checker', 'category' => 'domain-dns', 'title' => 'SSL certificate checker', 'description' => 'Validity, issuer, expiry, and hostname context.', 'execution_mode' => 'server', 'sort_order' => 90, 'is_popular' => true, 'input_schema' => ['fields' => [['name' => 'host', 'type' => 'text', 'label' => 'Host', 'placeholder' => 'example.com', 'required' => true], ['name' => 'port', 'type' => 'number', 'label' => 'Port', 'default' => 443]]]],
            ['slug' => 'ip-hosting-info', 'category' => 'domain-dns', 'title' => 'IP / hosting info', 'description' => 'IP, ASN, and hosting hints where data is available.', 'execution_mode' => 'server', 'sort_order' => 100, 'input_schema' => ['fields' => [['name' => 'query', 'type' => 'text', 'label' => 'IP or hostname', 'required' => true]]]],
            ['slug' => 'subdomain-visibility', 'category' => 'domain-dns', 'title' => 'Subdomain visibility', 'description' => 'Passive hints from public DNS where supported.', 'execution_mode' => 'server', 'sort_order' => 110, 'is_new' => true, 'input_schema' => ['fields' => [['name' => 'domain', 'type' => 'text', 'label' => 'Root domain', 'required' => true]]]],
            ['slug' => 'link-safety-summary', 'category' => 'security-trust', 'title' => 'Link safety summary', 'description' => 'Informational trust signals for a URL (not a malware guarantee).', 'execution_mode' => 'server', 'sort_order' => 120, 'input_schema' => ['fields' => [['name' => 'url', 'type' => 'url', 'label' => 'URL', 'required' => true]]]],
            ['slug' => 'phishing-suspicion', 'category' => 'security-trust', 'title' => 'Phishing suspicion checker', 'description' => 'Heuristics and basic indicators for suspicious URLs.', 'execution_mode' => 'server', 'sort_order' => 130, 'input_schema' => ['fields' => [['name' => 'url', 'type' => 'url', 'label' => 'URL', 'required' => true]]]],
            ['slug' => 'security-headers', 'category' => 'security-trust', 'title' => 'Security header checker', 'description' => 'Common HTTP security headers for a URL.', 'execution_mode' => 'server', 'sort_order' => 140, 'input_schema' => ['fields' => [['name' => 'url', 'type' => 'url', 'label' => 'URL', 'required' => true]]]],
            ['slug' => 'email-security-records', 'category' => 'security-trust', 'title' => 'Email security records', 'description' => 'SPF, DMARC, and related DNS visibility.', 'execution_mode' => 'server', 'sort_order' => 150, 'input_schema' => ['fields' => [['name' => 'domain', 'type' => 'text', 'label' => 'Domain', 'required' => true]]]],
            ['slug' => 'password-strength', 'category' => 'security-trust', 'title' => 'Password strength', 'description' => 'Client-side strength analysis; nothing is sent to our servers.', 'execution_mode' => 'client', 'sort_order' => 160, 'input_schema' => ['fields' => [['name' => 'password', 'type' => 'password', 'label' => 'Password', 'client_only' => true]]]],
            ['slug' => 'public-exposure-snapshot', 'category' => 'security-trust', 'title' => 'Public exposure snapshot', 'description' => 'Non-destructive, informational summary for a domain.', 'execution_mode' => 'server', 'sort_order' => 170, 'is_new' => true, 'input_schema' => ['fields' => [['name' => 'domain', 'type' => 'text', 'label' => 'Domain', 'required' => true]]]],
            ['slug' => 'image-compress', 'category' => 'media', 'title' => 'Image compressor', 'description' => 'Shrink images in the browser.', 'execution_mode' => 'client', 'sort_order' => 180, 'input_schema' => ['fields' => [['name' => 'file', 'type' => 'file', 'label' => 'Image', 'accept' => 'image/*']]]],
            ['slug' => 'image-convert', 'category' => 'media', 'title' => 'Image converter', 'description' => 'Convert between JPG, PNG, and WebP locally.', 'execution_mode' => 'client', 'sort_order' => 190, 'input_schema' => ['fields' => [['name' => 'file', 'type' => 'file', 'label' => 'Image']]]],
            ['slug' => 'pdf-to-images', 'category' => 'media', 'title' => 'PDF to images', 'description' => 'Rasterize pages from a PDF you upload (client-side).', 'execution_mode' => 'client', 'sort_order' => 200, 'input_schema' => ['fields' => [['name' => 'file', 'type' => 'file', 'label' => 'PDF', 'accept' => 'application/pdf']]]],
            ['slug' => 'file-meta', 'category' => 'media', 'title' => 'File size & media info', 'description' => 'Basic metadata for files you select locally.', 'execution_mode' => 'client', 'sort_order' => 210, 'input_schema' => ['fields' => [['name' => 'file', 'type' => 'file', 'label' => 'File']]]],
            ['slug' => 'meta-tag-checker', 'category' => 'seo-content', 'title' => 'Meta tag checker', 'description' => 'Title, description, Open Graph, canonical from a URL.', 'execution_mode' => 'server', 'sort_order' => 220, 'is_popular' => true, 'input_schema' => ['fields' => [['name' => 'url', 'type' => 'url', 'label' => 'URL', 'required' => true]]]],
            ['slug' => 'keyword-density', 'category' => 'seo-content', 'title' => 'Keyword density', 'description' => 'Paste text or fetch a page (where supported) for term frequency.', 'execution_mode' => 'server', 'sort_order' => 230, 'input_schema' => ['fields' => [['name' => 'text', 'type' => 'textarea', 'label' => 'Text']]]],
            ['slug' => 'readability', 'category' => 'seo-content', 'title' => 'Readability checker', 'description' => 'Simple readability metrics for pasted text.', 'execution_mode' => 'client', 'sort_order' => 240, 'input_schema' => ['fields' => [['name' => 'text', 'type' => 'textarea', 'label' => 'Text', 'required' => true]]]],
            ['slug' => 'slug-generator', 'category' => 'seo-content', 'title' => 'Slug / URL generator', 'description' => 'Turn a title into a clean slug.', 'execution_mode' => 'client', 'sort_order' => 250, 'input_schema' => ['fields' => [['name' => 'title', 'type' => 'text', 'label' => 'Title', 'required' => true]]]],
            ['slug' => 'sitemap-robots-check', 'category' => 'seo-content', 'title' => 'Sitemap & robots.txt', 'description' => 'Check robots.txt and discover sitemap hints.', 'execution_mode' => 'server', 'sort_order' => 260, 'input_schema' => ['fields' => [['name' => 'url', 'type' => 'url', 'label' => 'Site base URL', 'required' => true]]]],
            ['slug' => 'schema-markup-check', 'category' => 'seo-content', 'title' => 'Schema markup hints', 'description' => 'Detect JSON-LD presence on a page.', 'execution_mode' => 'server', 'sort_order' => 270, 'is_new' => true, 'input_schema' => ['fields' => [['name' => 'url', 'type' => 'url', 'label' => 'URL', 'required' => true]]]],
            ['slug' => 'json-formatter', 'category' => 'developer', 'title' => 'JSON formatter & validator', 'description' => 'Format and validate JSON in the browser.', 'execution_mode' => 'client', 'sort_order' => 280, 'is_popular' => true, 'input_schema' => ['fields' => [['name' => 'json', 'type' => 'textarea', 'label' => 'JSON', 'required' => true]]]],
            ['slug' => 'base64', 'category' => 'developer', 'title' => 'Base64 encoder / decoder', 'description' => 'Encode or decode Base64 locally.', 'execution_mode' => 'client', 'sort_order' => 290, 'input_schema' => ['fields' => [['name' => 'text', 'type' => 'textarea', 'label' => 'Text'], ['name' => 'mode', 'type' => 'select', 'label' => 'Mode', 'options' => ['encode' => 'Encode', 'decode' => 'Decode']]]]],
            ['slug' => 'jwt-decode', 'category' => 'developer', 'title' => 'JWT decoder', 'description' => 'Decode JWT payload (signature not verified).', 'execution_mode' => 'client', 'sort_order' => 300, 'input_schema' => ['fields' => [['name' => 'token', 'type' => 'textarea', 'label' => 'JWT', 'required' => true]]]],
            ['slug' => 'uuid-generator', 'category' => 'developer', 'title' => 'UUID generator', 'description' => 'Generate UUID v4 values.', 'execution_mode' => 'client', 'sort_order' => 310, 'input_schema' => ['fields' => [['name' => 'count', 'type' => 'number', 'label' => 'Count', 'default' => 5]]]],
            ['slug' => 'timestamp-converter', 'category' => 'developer', 'title' => 'Timestamp converter', 'description' => 'Unix time, ISO, and local previews.', 'execution_mode' => 'client', 'sort_order' => 320, 'input_schema' => ['fields' => [['name' => 'input', 'type' => 'text', 'label' => 'Timestamp or date string']]]],
            ['slug' => 'utm-builder', 'category' => 'marketing', 'title' => 'UTM builder', 'description' => 'Build tagged campaign URLs.', 'execution_mode' => 'client', 'sort_order' => 330, 'input_schema' => ['fields' => [['name' => 'base_url', 'type' => 'url', 'label' => 'Base URL', 'required' => true], ['name' => 'utm_source', 'type' => 'text', 'label' => 'utm_source'], ['name' => 'utm_medium', 'type' => 'text', 'label' => 'utm_medium'], ['name' => 'utm_campaign', 'type' => 'text', 'label' => 'utm_campaign']]]],
            ['slug' => 'social-preview-check', 'category' => 'marketing', 'title' => 'Social preview checker', 'description' => 'Summarize OG/Twitter tags from a URL.', 'execution_mode' => 'server', 'sort_order' => 340, 'input_schema' => ['fields' => [['name' => 'url', 'type' => 'url', 'label' => 'URL', 'required' => true]]]],
        ];

        foreach ($rows as $row) {
            $categoryId = MicroToolCategory::query()->where('slug', $row['category'])->value('id');

            MicroTool::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'micro_tool_category_id' => $categoryId,
                    'category' => $row['category'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'icon_key' => null,
                    'execution_mode' => $row['execution_mode'],
                    'limits' => $row['limits'] ?? null,
                    'input_schema' => $row['input_schema'] ?? null,
                    'is_active' => true,
                    'is_premium' => false,
                    'sort_order' => $row['sort_order'],
                    'released_at' => $now,
                    'featured_score' => ! empty($row['is_popular']) ? 10 : 0,
                    'is_popular' => (bool) ($row['is_popular'] ?? false),
                    'is_new' => (bool) ($row['is_new'] ?? false),
                ]
            );
        }
    }
}
