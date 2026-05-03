<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        $this->bootstrapTestingEnvironment();

        $app = require Application::inferBasePath().'/bootstrap/app.php';

        $this->traitsUsedByTest = array_flip(class_uses_recursive(static::class));

        /*
         * Apply after config files load so cache + DB default match sqlite :memory: before
         * services (e.g. Spatie PermissionRegistrar) resolve the cache store.
         */
        $app->afterBootstrapping(LoadConfiguration::class, function ($app): void {
            $app['config']->set([
                'database.default' => 'sqlite',
                'database.connections.sqlite.database' => ':memory:',
                'database.connections.sqlite.url' => null,
                'cache.default' => 'array',
                'permission.cache.store' => 'array',
                'queue.default' => 'sync',
                'session.driver' => 'array',
                'captcha.bypass' => true,
            ]);
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Ensure PHPUnit / CLI sees sqlite before Dotenv runs (helps Windows env ordering).
     */
    private function bootstrapTestingEnvironment(): void
    {
        $vars = [
            'APP_ENV' => 'testing',
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => ':memory:',
            'DB_URL' => '',
            'DB_HOST' => '',
            'CACHE_STORE' => 'array',
        ];

        foreach ($vars as $key => $value) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    protected function tearDown(): void
    {
        // PHPUnit wraps each test in an output buffer; nested buffers from the
        // app (e.g. Blade) must be closed or PHPUnit 12 marks the test risky.
        while (ob_get_level() > 1) {
            ob_end_clean();
        }

        parent::tearDown();
    }
}
