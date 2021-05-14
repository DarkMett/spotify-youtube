<?php

namespace App\Http\Middleware;

use Closure;

class SpotifyAuthenticate
{
    public function handle($request, Closure $next)
    {
        if (!session('spotify_access_token')) {
            return redirect(route('login'));
        }

        return $next($request);
    }
}
