<?php

namespace App\Http\Middleware;

use Closure;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (self::isAdminOrFail()) {
            return $next($request);
        }

        abort(403);
    }

    /**
     * Ensure current user has admin role, otherwise logout user.
     *
     * @return bool
     */
    public static function isAdminOrFail()
    {
        if (! $user = auth()->user()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        auth()->guard()->logout();

        return false;
    }
}
