<?php

namespace App\Support;

final class PagePath
{
    public static function toHref(string $path): string
    {
        if ($path === 'home') {
            return '/';
        }

        return '/'.ltrim($path, '/');
    }
}
