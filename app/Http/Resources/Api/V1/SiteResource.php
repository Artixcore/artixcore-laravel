<?php

namespace App\Http\Resources\Api\V1;

use App\Models\MediaAsset;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SiteSetting
 */
class SiteResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var SiteSetting $s */
        $s = $this->resource;

        return [
            'site_name' => $s->site_name,
            'default_meta_title' => $s->default_meta_title,
            'default_meta_description' => $s->default_meta_description,
            'contact_email' => $s->contact_email,
            'social_links' => $s->social_links ?? [],
            'design_tokens' => $s->design_tokens ?? (object) [],
            'theme_default' => $s->theme_default ?? 'system',
            'logo' => $this->mediaPayload($s->relationLoaded('logoMedia') ? $s->logoMedia : $s->logoMedia()->first()),
            'favicon_url' => $this->mediaUrl($s->relationLoaded('faviconMedia') ? $s->faviconMedia : $s->faviconMedia()->first()),
            'og_default' => $this->mediaPayload($s->relationLoaded('ogDefaultMedia') ? $s->ogDefaultMedia : $s->ogDefaultMedia()->first()),
        ];
    }

    /**
     * @return array{url: string, alt: string|null}|null
     */
    private function mediaPayload(?MediaAsset $media): ?array
    {
        if ($media === null) {
            return null;
        }

        return [
            'url' => $media->absoluteUrl(),
            'alt' => $media->alt_text,
        ];
    }

    private function mediaUrl(?MediaAsset $media): ?string
    {
        if ($media === null) {
            return null;
        }

        return $media->absoluteUrl();
    }
}
