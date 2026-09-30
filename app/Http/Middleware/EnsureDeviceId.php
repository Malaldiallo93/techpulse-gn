<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Identifiant anonyme d'appareil : sert à mémoriser rappels, progression et participations
 * sans demander de compte au lecteur. Aucune donnée personnelle n'y est associée.
 */
class EnsureDeviceId
{
    public const COOKIE = 'techpulse_device';

    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->cookie(self::COOKIE);
        if (! is_string($id) || ! preg_match('/^[a-f0-9-]{36}$/', $id)) {
            $id = (string) Str::uuid();
            Cookie::queue(self::COOKIE, $id, 60 * 24 * 365 * 2, null, null, null, true, false, 'lax');
        }
        $request->attributes->set('device', $id);

        return $next($request);
    }
}
