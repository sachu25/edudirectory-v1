<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsUpgraded
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->force_password_change) {
            if (! $request->routeIs('force-password-change.*') && ! $request->routeIs('logout')) {
                return redirect()->route('force-password-change.notice')
                    ->with('warning', 'Security requirement: Please update your password to meet strict security standards before continuing.');
            }
        }

        return $next($request);
    }
}
