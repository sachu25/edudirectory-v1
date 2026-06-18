<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();

        if ($user->status !== 'active') {
            auth()->logout();
            return redirect('login')->with('error', 'Your account is inactive.');
        }

        if (!empty($roles) && (!$user->role || !in_array($user->role->name, $roles))) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
