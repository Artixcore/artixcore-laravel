<?php

namespace App\Services\Ai;

use App\Models\AiProvider;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\Request;

class AiProviderService
{
    public function __construct(private ActivityLogger $activityLog) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, Request $request): AiProvider
    {
        $provider = new AiProvider;
        $this->applyAttributes($provider, $data, true);
        $provider->save();

        $this->activityLog->log('ai_provider.created', $provider, [
            'driver' => $provider->driver,
            'name' => $provider->name,
        ], $request);

        return $provider;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(AiProvider $provider, array $data, Request $request): void
    {
        $beforeDriver = $provider->driver;
        $this->applyAttributes($provider, $data, false);
        $provider->save();

        $this->activityLog->log('ai_provider.updated', $provider, [
            'driver' => $provider->driver,
            'was_driver' => $beforeDriver,
            'key_rotated' => array_key_exists('api_key', $data) && $data['api_key'] !== null && $data['api_key'] !== '',
        ], $request);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function applyAttributes(AiProvider $provider, array $data, bool $creating): void
    {
        $fillable = [
            'name', 'driver', 'is_enabled', 'default_model', 'base_url',
            'timeout_seconds', 'priority', 'max_output_tokens', 'rate_limit_json', 'metadata',
        ];

        foreach ($fillable as $key) {
            if (array_key_exists($key, $data)) {
                $provider->{$key} = $data[$key];
            }
        }

        if (array_key_exists('api_key', $data)) {
            $key = $data['api_key'];
            if (is_string($key) && $key !== '') {
                $provider->api_key_encrypted = $key;
                $provider->api_key_hint = strlen($key) >= 4 ? substr($key, -4) : '****';
            }
        } elseif ($creating) {
            $provider->api_key_encrypted = null;
            $provider->api_key_hint = null;
        }
    }
}
