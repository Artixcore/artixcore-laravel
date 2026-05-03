<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Support\MarketingContent;
use Illuminate\Database\Seeder;

/**
 * Safe, idempotent repairs for known-broken marketing defaults (escaped entities, legacy hero typing,
 * corrupted paths). Run after deploy if production content predates fixes:
 *
 *   php artisan db:seed --class=ProductionContentRepairSeeder --force
 *
 * Does not delete unrelated rows or run destructive migrations.
 */
class ProductionContentRepairSeeder extends Seeder
{
    /** @deprecated Seeded strings replaced by config/marketing.php */
    private const LEGACY_META_TITLE = 'Artixcore — SaaS, AI, Web3 & custom software';

    private const LEGACY_META_DESCRIPTION_PREFIX = 'We build SaaS platforms, AI agent systems';

    private const LEGACY_ABOUT_META_TITLE = 'About — Artixcore';

    private const LEGACY_SERVICES_META_TITLE = 'Services — Artixcore';

    private const LEGACY_SAAS_META_TITLE = 'SaaS Platforms — Artixcore';

    public function run(): void
    {
        $settings = SiteSetting::instance();

        $this->repairContactEmail($settings);
        $this->repairSiteMetaDefaults($settings);
        $settings->homepage_content = $this->repairedHomepage($settings->homepage_content);
        $settings->about_content = $this->repairedAbout($settings->about_content);
        $settings->services_page_content = $this->repairedServicesPage($settings->services_page_content);
        $settings->saas_page_content = $this->repairedSaaSPage($settings->saas_page_content);

        $settings->save();
    }

    private function repairContactEmail(SiteSetting $settings): void
    {
        $email = trim((string) ($settings->contact_email ?? ''));
        if ($email === '' || str_ends_with($email, '@artixcore.test')) {
            $settings->contact_email = 'hello@artixcore.com';
        }
    }

    private function repairSiteMetaDefaults(SiteSetting $settings): void
    {
        $title = trim((string) ($settings->default_meta_title ?? ''));
        if ($title === '' || $title === self::LEGACY_META_TITLE || str_contains($title, 'Web3 & custom software')) {
            $settings->default_meta_title = config('marketing.homepage.meta_title');
        }

        $desc = trim((string) ($settings->default_meta_description ?? ''));
        if ($desc === '' || str_starts_with($desc, self::LEGACY_META_DESCRIPTION_PREFIX)) {
            $settings->default_meta_description = config('marketing.homepage.meta_description');
        }

        foreach ([$settings->default_meta_title, $settings->default_meta_description] as $chunk) {
            if (is_string($chunk) && $this->hasBrokenEscapingArtifacts($chunk)) {
                $settings->default_meta_title = config('marketing.homepage.meta_title');
                $settings->default_meta_description = config('marketing.homepage.meta_description');
                break;
            }
        }
    }

    /**
     * @param  array<string, mixed>|null  $stored
     * @return array<string, mixed>
     */
    private function repairedHomepage(?array $stored): array
    {
        $merged = MarketingContent::mergeHomepage(is_array($stored) ? $stored : []);
        $defaults = MarketingContent::defaultHomepage();

        $blob = json_encode($stored ?? []);
        $needsRepair = is_string($blob) && ($this->hasBrokenEscapingArtifacts($blob) || str_contains($blob, '../../index.html'));

        if (! $needsRepair && $this->homepageShowsLegacyTypingArtifacts($merged)) {
            $needsRepair = true;
        }

        if (! $needsRepair && trim((string) ($merged['hero_title'] ?? '')) === '') {
            $needsRepair = true;
        }

        if (! $needsRepair) {
            foreach (['hero_badge', 'hero_subtitle', 'hero_title_prefix'] as $key) {
                $v = $merged[$key] ?? '';
                if (is_string($v) && $this->hasBrokenEscapingArtifacts($v)) {
                    $needsRepair = true;
                    break;
                }
            }
        }

        if (! $needsRepair) {
            return $merged;
        }

        return array_replace($merged, [
            'hero_badge' => $defaults['hero_badge'],
            'hero_title' => $defaults['hero_title'],
            'hero_title_prefix' => $defaults['hero_title_prefix'],
            'hero_typed_phrases' => $defaults['hero_typed_phrases'],
            'hero_subtitle' => $defaults['hero_subtitle'],
            'hero_trust_line' => $defaults['hero_trust_line'],
            'hero_primary_cta_label' => $defaults['hero_primary_cta_label'],
            'hero_primary_cta_url' => $defaults['hero_primary_cta_url'],
            'hero_secondary_cta_label' => $defaults['hero_secondary_cta_label'],
            'hero_secondary_cta_url' => $defaults['hero_secondary_cta_url'],
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $stored
     * @return array<string, mixed>
     */
    private function repairedAbout(?array $stored): array
    {
        $merged = MarketingContent::mergeAbout(is_array($stored) ? $stored : []);
        $defaults = MarketingContent::defaultAbout();

        $metaTitle = (string) ($merged['meta_title'] ?? '');
        if ($metaTitle === self::LEGACY_ABOUT_META_TITLE || $this->hasBrokenEscapingArtifacts($metaTitle)) {
            return array_replace($merged, [
                'meta_title' => $defaults['meta_title'],
                'meta_description' => $defaults['meta_description'],
                'og_title' => $defaults['og_title'],
                'og_description' => $defaults['og_description'],
            ]);
        }

        return $merged;
    }

    /**
     * @param  array<string, mixed>|null  $stored
     * @return array<string, mixed>
     */
    private function repairedServicesPage(?array $stored): array
    {
        $merged = MarketingContent::mergeServicesPage(is_array($stored) ? $stored : []);
        $defaults = MarketingContent::defaultServicesPage();

        $metaTitle = (string) ($merged['meta_title'] ?? '');
        if (
            $metaTitle === self::LEGACY_SERVICES_META_TITLE
            || preg_match('/^Services — Artixcore$/', $metaTitle) === 1
            || $this->hasBrokenEscapingArtifacts($metaTitle)
            || $this->hasBrokenEscapingArtifacts((string) ($merged['meta_description'] ?? ''))
        ) {
            return array_replace($merged, [
                'meta_title' => $defaults['meta_title'],
                'meta_description' => $defaults['meta_description'],
                'og_title' => $defaults['og_title'],
                'og_description' => $defaults['og_description'],
            ]);
        }

        return $merged;
    }

    /**
     * @param  array<string, mixed>|null  $stored
     * @return array<string, mixed>
     */
    private function repairedSaaSPage(?array $stored): array
    {
        $merged = MarketingContent::mergeSaaSPage(is_array($stored) ? $stored : []);
        $defaults = MarketingContent::defaultSaaSPage();

        $metaTitle = (string) ($merged['meta_title'] ?? '');
        if (
            $metaTitle === self::LEGACY_SAAS_META_TITLE
            || preg_match('/^SaaS Platforms — Artixcore$/', $metaTitle) === 1
            || $this->hasBrokenEscapingArtifacts($metaTitle)
            || $this->hasBrokenEscapingArtifacts((string) ($merged['meta_description'] ?? ''))
        ) {
            return array_replace($merged, [
                'meta_title' => $defaults['meta_title'],
                'meta_description' => $defaults['meta_description'],
                'og_title' => $defaults['og_title'],
                'og_description' => $defaults['og_description'],
            ]);
        }

        return $merged;
    }

    private function hasBrokenEscapingArtifacts(string $value): bool
    {
        return str_contains($value, '&amp;&amp;')
            || str_contains($value, '../../index.html');
    }

    /**
     * Legacy homepage stored rotating phrases for ityped; empty hero_title + typed phrases caused fragile output.
     *
     * @param  array<string, mixed>  $merged
     */
    private function homepageShowsLegacyTypingArtifacts(array $merged): bool
    {
        $typed = $merged['hero_typed_phrases'] ?? null;
        if ($typed !== null && ! is_array($typed)) {
            return true;
        }

        $title = trim((string) ($merged['hero_title'] ?? ''));
        $prefix = trim((string) ($merged['hero_title_prefix'] ?? ''));

        return ($title === '' && is_array($typed) && $typed !== [])
            || ($title === '' && $prefix !== '');
    }
}
