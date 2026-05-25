<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeMiddleware
{
    public function handle(Request $request, Closure $next, $mode)
    {
        if (!Auth::check()) {
            abort(403);
        }

        if (active_mode() !== $mode) {
            abort(403, 'Mode tidak sesuai.');
        }

        return $next($request);
    }
}

