<?php

namespace App\Providers;

use App\Services\Tools\Handlers\DnsLookupHandler;
use App\Services\Tools\Handlers\EmailSecurityRecordsHandler;
use App\Services\Tools\Handlers\IpHostingInfoHandler;
use App\Services\Tools\Handlers\KeywordDensityHandler;
use App\Services\Tools\Handlers\LinkSafetySummaryHandler;
use App\Services\Tools\Handlers\MetaTagCheckerHandler;
use App\Services\Tools\Handlers\MobileFriendlyHintsHandler;
use App\Services\Tools\Handlers\PhishingSuspicionHandler;
use App\Services\Tools\Handlers\PublicExposureSnapshotHandler;
use App\Services\Tools\Handlers\SchemaMarkupCheckerHandler;
use App\Services\Tools\Handlers\SecurityHeadersHandler;
use App\Services\Tools\Handlers\SitemapRobotsCheckerHandler;
use App\Services\Tools\Handlers\SocialPreviewCheckerHandler;
use App\Services\Tools\Handlers\SpeedSnapshotHandler;
use App\Services\Tools\Handlers\SslCheckerHandler;
use App\Services\Tools\Handlers\UptimeCheckHandler;
use App\Services\Tools\Handlers\WebsiteAuditBasicHandler;
use App\Services\Tools\Handlers\WebsiteTechnologyHandler;
use App\Services\Tools\ToolRegistry;
use Illuminate\Support\ServiceProvider;

class MicroToolsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ToolRegistry::class, function (): ToolRegistry {
            return new ToolRegistry([
                'dns-lookup' => DnsLookupHandler::class,
                'ssl-checker' => SslCheckerHandler::class,
                'uptime-check' => UptimeCheckHandler::class,
                'meta-tag-checker' => MetaTagCheckerHandler::class,
                'social-preview-check' => SocialPreviewCheckerHandler::class,
                'website-audit-basic' => WebsiteAuditBasicHandler::class,
                'speed-snapshot' => SpeedSnapshotHandler::class,
                'website-technology' => WebsiteTechnologyHandler::class,
                'mobile-friendly-hints' => MobileFriendlyHintsHandler::class,
                'schema-markup-check' => SchemaMarkupCheckerHandler::class,
                'security-headers' => SecurityHeadersHandler::class,
                'email-security-records' => EmailSecurityRecordsHandler::class,
                'sitemap-robots-check' => SitemapRobotsCheckerHandler::class,
                'keyword-density' => KeywordDensityHandler::class,
                'phishing-suspicion' => PhishingSuspicionHandler::class,
                'link-safety-summary' => LinkSafetySummaryHandler::class,
                'public-exposure-snapshot' => PublicExposureSnapshotHandler::class,
                'ip-hosting-info' => IpHostingInfoHandler::class,
            ]);
        });
    }
}
