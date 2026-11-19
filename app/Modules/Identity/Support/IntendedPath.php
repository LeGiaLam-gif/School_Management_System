<?php

namespace App\Modules\Identity\Support;

use Illuminate\Http\Request;

final class IntendedPath
{
    public static function pull(Request $request): string
    {
        $intended = $request->session()->pull('url.intended');

        if (! is_string($intended) || trim($intended) === '') {
            return '/';
        }

        $parts = parse_url($intended);

        if ($parts === false) {
            return '/';
        }

        if (isset($parts['scheme']) || isset($parts['host'])) {
            $scheme = strtolower($parts['scheme'] ?? '');
            $host = strtolower($parts['host'] ?? '');
            $appHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));

            if (! in_array($scheme, ['http', 'https'], true)
                || $host === ''
                || ! in_array($host, [strtolower($request->getHost()), $appHost], true)) {
                return '/';
            }
        }

        $path = $parts['path'] ?? '/';

        if (! str_starts_with($path, '/') || preg_match('~^/(?:https?|ftp):~i', $path) === 1) {
            return '/';
        }

        return $path.(isset($parts['query']) ? '?'.$parts['query'] : '');
    }
}
