<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
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
